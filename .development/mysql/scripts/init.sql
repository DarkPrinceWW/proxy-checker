# create databases
CREATE DATABASE IF NOT EXISTS proxy;
CREATE DATABASE IF NOT EXISTS proxy_testing;

# create db users
CREATE USER 'proxy'@'%' IDENTIFIED BY 'secret';
CREATE USER 'proxy_testing'@'%' IDENTIFIED BY 'secret';

# grant privileges
GRANT ALL ON proxy.* TO 'proxy'@'%';
GRANT ALL ON proxy_testing.* TO 'proxy_testing'@'%';

# flush privileges to apply changes
FLUSH PRIVILEGES;
