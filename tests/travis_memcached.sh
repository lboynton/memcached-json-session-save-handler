sudo apt-get install libevent-dev libcloog-ppl0
wget https://launchpad.net/libmemcached/1.0/1.0.13/+download/libmemcached-1.0.13.tar.gz
tar xzf libmemcached-1.0.13.tar.gz
cd libmemcached-1.0.13
./configure && make && sudo make install
printf "\n" | pecl install memcached
echo "extension=memcached.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
