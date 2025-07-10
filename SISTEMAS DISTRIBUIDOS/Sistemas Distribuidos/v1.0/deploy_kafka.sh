gnome-terminal -- bash -c "./bin/zookeeper-server-start.sh ./config/zookeeper.properties; exec bash" &

sleep 5

gnome-terminal -- bash -c "./bin/kafka-server-start.sh ./config/server.properties; exec bash" &

sleep 10

./bin/kafka-topics.sh --create --topic customer_responses --bootstrap-server localhost:9092
./bin/kafka-topics.sh --create --topic taxi_requests --bootstrap-server localhost:9092
./bin/kafka-topics.sh --create --topic taxi_status --bootstrap-server localhost:9092
./bin/kafka-topics.sh --create --topic taxi_instructions --bootstrap-server localhost:9092
./bin/kafka-topics.sh --create --topic map_updates --bootstrap-server localhost:9092


