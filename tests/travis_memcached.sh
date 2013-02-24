sudo apt-get install libevent-dev libcloog-ppl0
wget https://launchpad.net/libmemcached/1.0/1.0.16/+download/libmemcached-1.0.16.tar.gz
tar xzf libmemcached-1.0.16.tar.gz
cd libmemcached-1.0.16
./configure && make && sudo make install
printf "\n" | pecl install memcached
