## Clustering

Cluster support adds high availability and scaling features to appserver.io. High availability means to handle client requests for a minimal down and an acceptable response time. To guarantee this, appserver.io **must** be able to distribute requests to nodes available in the cluster and make sure, that a failover node has the requested data available. As some components of an application may be stateful, replication must take care, that data will be available on more than one node or can be restored from several nodes if necessary.
