{
  pkgs,
  lib,
  ...
}: let
  # shared libraries for Playwright/Chrome browser automation
  browserLibs = with pkgs; [
    glib
    gtk3
    cairo
    pango
    at-spi2-core
    at-spi2-atk
    nss
    nspr
    cups
    dbus
    expat
    mesa
    libdrm
    libxcb
    libxkbcommon
    libgbm
    libX11
    libXcomposite
    libXdamage
    libXext
    libXfixes
    libXrandr
    alsa-lib
    udev
  ];
in {
  services.mysql = {
    enable = true;
    package = pkgs.mariadb;
    settings = {
      mysqldump = {
        quick = true;
        max_allowed_packet = "16M";
      };
    };
    initialDatabases = [
      {
        name = "rms";
        schema = ./sql/schema.sql;
      }
    ];
    ensureUsers = [
      {
        name = "rms";
        ensurePermissions = {
          "rms.*" = "ALL PRIVILEGES";
        };
      }
    ];
  };

  packages = browserLibs;

  # make the lib dirs resolvable by the dynamic linker (chrome-headless-shell etc.)
  env.LD_LIBRARY_PATH = lib.makeLibraryPath browserLibs;
}
