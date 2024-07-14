# Case Study

## Scenario

Our system has been experiencing intermittent slowdowns during peak hours, impacting user experience. You are tasked with investigating and resolving the issue.

## Information Provided

* System logs indicate increased database queries during peak hours.
* The application server is utilizing a high percentage of CPU resources.

## 1. What are your initial steps to diagnose the performance bottleneck?

* **Define Performance Goals** - Establish clear performance targets, such as maximum response times and the number of concurrent users the server should handle.
* **Set Up the Testing Environment** - Replicate the production environment with the same hardware specifications, software versions, network setup, and database configuration.
* **Baseline Testing** - Conduct tests to understand how the system performs under normal conditions. This provides a reference point for comparison.
* **Monitor Key Resources** - Track CPU, memory, network, and disk I/O to identify which resources are being heavily utilized and may be causing bottlenecks.
* **Load Testing** - Simulate peak loads to stress the system and identify performance issues that only arise under high traffic.
* **Identify Potential Bottlenecks** - Analyze the test results to pinpoint areas where performance lags, such as slow-running queries or high memory usage.
* **In-Depth Analysis** - Use diagnostic tools like Datadog, Dynatrace, or New Relic to gather detailed performance metrics and logs.
* **Pinpoint the Bottleneck** - Determine whether the bottleneck is due to insufficient resources, poorly designed queries, or other factors.
* **Optimization** - Apply fixes such as query tuning, system reconfiguration, or hardware upgrades to address the identified bottlenecks.
* **Retest and Validate** - After making changes, retest to ensure that the performance has improved and the bottlenecks have been resolved.

*Disclaimer: the points above are compiled through research on the internet.*

## 2. Describe potential causes for the increased database queries and high CPU usage.

* **Traffic** - Increase in site traffic, thus more queries are being made
* **Caching** - Insufficient caching between the application and the database.
* **Database Optimization** - Lack of proper indexing, query optimization, and proper database configuration.
* **Hardware** - Insufficient RAM, slow storage solutions, or low spec CPU.

## 3. Outline some strategies to optimize the application's performance for handling peak loads.

* **Load Balancing** - Distribute the workload across multiple servers to ensure optimal resource utilization and improved performance. This helps prevent any single server from becoming a bottleneck.
* **Caching** - Implement caching strategies using tools like Redis to reduce server load and improve response times. Caching stores frequently accessed data in memory, allowing for quicker retrieval.
* **Content Delivery Network (CDN)** - Offload assets to another server and front it with a CDN to decrease server load, and improve performance.
* **Database Optimization** - Regularly fine-tune the database systems like MySQL to enhance query performance and reduce resource consumption. This includes indexing, query optimization, and proper database configuration.
* **Database Replication** - Setup primary and replica database to handle different kind of work load. Primary for insert, update or delete, while replica for select.
* **Code Optimization** - Review and optimize the application code to be more efficient. This can involve refactoring, removing unnecessary computations, and optimizing algorithms.
* **Monitoring Tools** - Utilize performance monitoring software such as New Relic or Nagios to analyze server performance, identify bottlenecks, and gain insights to enhance efficiency2.
* **Hardware Upgrades** - Upgrade the physical or virtual server’s hardware by adding more RAM, using faster storage solutions, or upgrading the CPU.
* **Autoscaling** - Auto scale up (add more RAM, CPU) or scale out (add more server) the servers to handle incoming load.

## 4. How would you ensure the system remains available and reliable during troubleshooting?

* **Perform Rolling Updates** - When applying fixes, do so incrementally rather than all at once. Update a few servers at a time to ensure that the system remains operational if an update causes issues.
* **Monitor Continuously** - Use monitoring tools to keep an eye on the system’s performance in real-time. This helps in quickly identifying any negative impact caused by the changes made during troubleshooting.
* **Maintain a Staging Environment** - Test all changes in a staging environment that mirrors the production system before applying them to the live system.
* **Have a Rollback Plan** - Always have a plan to quickly revert changes if new issues arise after applying a fix.

