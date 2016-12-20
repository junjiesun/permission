grant all privileges on putao_permission.* to permission@'192.168.%' identified by 'permission';
grant all privileges on putao_permission.* to permission@'localhost' identified by 'permission';

grant select on putao_permission.* to readonly@'192.168.%' identified by 'readonly';
grant select on putao_permission.* to readonly@'localhost' identified by 'readonly';