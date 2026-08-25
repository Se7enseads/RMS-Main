<?php

/**
 * @var \App\Models\Order $order
 * @var array<int, \App\Models\OrderItem> $items
 * @var array<string, mixed> $restaurant
 */

function escapeReceipt(string $text): string
{
    return strtr($text, [
        '\\' => '\\\\',
        '|' => '\\|',
        '{' => '\\{',
        '}' => '\\}',
        '~' => '\\~',
        '_' => '\\_',
        '"' => '\\"',
        '`' => '\\`',
        '^' => '\\^',
        '-' => '\\-',
    ]);
}

$name = $restaurant['name'] ?? 'RMS Restaurant';
$address = (string)($restaurant['address'] ?? '');
$phone = (string)($restaurant['phone'] ?? '');
$currency = (string)($restaurant['currency'] ?? 'KES');

$lines = [];
$lines[] = '^^^RECEIPT';
$lines[] = '';
$lines[] = '|' . escapeReceipt($name) . '|';
if ($address !== '') {
    $lines[] = '|' . escapeReceipt($address) . '|';
}
if ($phone !== '') {
    $lines[] = '|' . escapeReceipt($phone) . '|';
}
$lines[] = '---';
$lines[] = 'ORDER: ' . escapeReceipt($order->orderNumber) . ' | ' . date('Y-m-d H:i', strtotime($order->createdAt ?? 'now'));
$lines[] = ($order->tableNumber !== null ? 'Table ' . $order->tableNumber : $order->type) . ' | Waiter: ' . escapeReceipt($order->userName ?? '—');
$lines[] = '---';

foreach ($items as $item) {
    $lines[] = escapeReceipt((string)$item->menuItemName) . ' | ' . $item->quantity . ' | ' . number_format($item->priceAtTime * $item->quantity, 2);
}

$lines[] = '---';
$lines[] = '^TOTAL | ^' . $currency . ' ' . number_format($order->totalAmount, 2);
$lines[] = '';
$lines[] = $order->isPaid ? ('PAID | ' . ($order->paymentMethod ?? 'CASH')) : 'NOT PAID';
$lines[] = '';
$lines[] = 'Thank you for dining with us!';

$markdown = implode("\n", $lines);
?>

<div class="bill-page">
    <div class="bill-toolbar no-print">
        <button type="button" class="button button-primary" onclick="window.print()">Print Bill</button>
        <a href="/kiosk" class="button-outline">Back to Dashboard</a>
        <p class="bill-hint">If the print dialog did not open automatically, click Print Bill.</p>
    </div>
    <div id="bill-receipt" class="bill-receipt" aria-label="Bill for <?= htmlspecialchars($order->orderNumber) ?>"></div>
</div>

<script src="/vendor/receipt/receipt.js"></script>
<script>
    const billMarkdown = <?= json_encode($markdown) ?>;

    window.addEventListener('load', async () => {
        const receipt = Receipt.from(billMarkdown, '-c 42 -l en');
        const svg = await receipt.toSVG();
        document.getElementById('bill-receipt').innerHTML = svg;
        window.print();
    });
</script>