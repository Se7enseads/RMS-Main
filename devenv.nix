{pkgs, ...}: {
  services.mysql = {
    enable = true;
    package = pkgs.mariadb;
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
}
