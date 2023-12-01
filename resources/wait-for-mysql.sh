set -e

while ! mysqladmin ping -h stark_mysql --silent; do
  echo >&2 "Mysql is down - sleep"
  sleep 5
done

echo >&2 "Mysql is up - executing command"
exec "$@"