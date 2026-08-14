<section class="kiosk-order">

    <?php if (!empty($error)) : ?>
        <div class="errors"><p><?= htmlspecialchars($error) ?></p></div>
    <?php endif ?>

    <div class="order-layout">

        <div class="menu-panel">
            <h1>Place Order</h1>
            <!--             TODO: Add an all button to show all the fetched menu items-->
            <div class="menu-tabs">
                <?php $first = true;
                foreach ($menu as $group) : ?>
                    <button type="button"
                            class="menu-tab <?= $first ? 'active' : '' ?>"
                            data-cat="<?= $group['category']->id ?>"
                            onclick="showCategory(this)"><?= htmlspecialchars($group['category']->name) ?></button>
                    <?php $first = false; endforeach ?>
            </div>

            <div class="menu-items">
                <?php foreach ($menu as $group) : ?>
                    <div class="menu-category" data-cat="<?= $group['category']->id ?>">
                        <?php foreach ($group['items'] as $item) : ?>
                            <button type="button" class="menu-item"
                                    onclick="addToCart(<?= $item->id ?>, '<?= htmlspecialchars($item->name, ENT_QUOTES) ?>', <?= $item->price ?>)">
                                <span class="menu-item-name"><?= htmlspecialchars($item->name) ?></span>
                                <span class="menu-item-price"><?= number_format($item->price, 2) ?></span>
                            </button>
                        <?php endforeach ?>
                        <?php if (empty($group['items'])) : ?>
                            <p class="muted">No items in this category.</p>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="cart-panel">
            <h2>Current Order</h2>

            <div id="cart-items" class="cart-items">
                <p class="muted">No items selected.</p>
            </div>

            <div class="cart-total">
                <span>Total</span>
                <span id="cart-total">0.00</span>
            </div>

            <form method="POST" action="/kiosk/order" id="order-form">
                <input type="hidden" name="order_type" id="order-type" value="DINE_IN">
                <input type="hidden" name="table_id" id="order-table" value="">
                <input type="hidden" name="items" id="order-items">

                <div class="order-options">
                    <label class="option">
                        <input type="radio" name="order_type_radio" value="DINE_IN" checked
                               onchange="setType(this.value)"> Dine In
                    </label>
                    <label class="option">
                        <input type="radio" name="order_type_radio" value="TAKEAWAY" onchange="setType(this.value)">
                        Takeaway
                    </label>
                    <label class="option">
                        <input type="radio" name="order_type_radio" value="DELIVERY" onchange="setType(this.value)">
                        Delivery
                    </label>
                </div>

                <div class="form-group" id="table-select-group">
                    <label>Table</label>
                    <select id="table-select" onchange="document.getElementById('order-table').value = this.value">
                        <option value="">Select a table</option>
                        <?php foreach ($tables as $table) : ?>
                            <option value="<?= $table->id ?>">Table <?= $table->number ?> (<?= $table->capacity ?>)
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <button type="submit" class="button button-primary place-order-btn" onclick="return submitOrder()">Place
                    Order
                </button>
            </form>
        </div>

    </div>
</section>

<script>
    let cart = {};

    function showCategory(btn) {
        document.querySelectorAll('.menu-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        let cat = btn.dataset.cat;
        document.querySelectorAll('.menu-category').forEach(c => {
            c.style.display = c.dataset.cat === cat ? '' : 'none';
        });
    }

    function addToCart(id, name, price) {
        if (!cart[id]) cart[id] = {id: id, name: name, price: price, qty: 0};
        cart[id].qty++;
        renderCart();
    }

    function changeQty(id, delta) {
        if (!cart[id]) return;
        cart[id].qty += delta;
        if (cart[id].qty <= 0) delete cart[id];
        renderCart();
    }

    function renderCart() {
        let box = document.getElementById('cart-items');
        let total = 0;
        let keys = Object.keys(cart);

        if (!keys.length) {
            box.innerHTML = '<p class="muted">No items selected.</p>';
            document.getElementById('cart-total').textContent = '0.00';
            return;
        }

        box.innerHTML = '';
        keys.forEach(function (k) {
            let item = cart[k];
            total += item.price * item.qty;
            let row = document.createElement('div');
            row.className = 'cart-item';
            row.innerHTML =
                '<span class="cart-name">' + item.name + '</span>' +
                '<span class="cart-price">' + item.price.toFixed(2) + '</span>' +
                '<span class="cart-qty">' +
                '<button type="button" onclick="changeQty(' + item.id + ', -1)">-</button>' +
                '<span>' + item.qty + '</span>' +
                '<button type="button" onclick="changeQty(' + item.id + ', 1)">+</button>' +
                '</span>' +
                '<span class="cart-line-total">' + (item.price * item.qty).toFixed(2) + '</span>';
            box.appendChild(row);
        });

        document.getElementById('cart-total').textContent = total.toFixed(2);
    }

    function setType(type) {
        document.getElementById('order-type').value = type;
        document.getElementById('table-select-group').style.display = type === 'DINE_IN' ? '' : 'none';
        if (type !== 'DINE_IN') document.getElementById('order-table').value = '';
    }

    function submitOrder() {
        let keys = Object.keys(cart);
        if (!keys.length) {
            alert('No items selected.');
            return false;
        }
        let type = document.getElementById('order-type').value;
        if (type === 'DINE_IN' && !document.getElementById('order-table').value) {
            alert('Please select a table.');
            return false;
        }
        let items = keys.map(function (k) {
            return {menu_item_id: parseInt(k), quantity: cart[k].qty};
        });
        document.getElementById('order-items').value = JSON.stringify(items);
        return true;
    }
</script>
