const mongoose = require("mongoose");
const dns = require("dns").promises;

/**
 * Connect to MongoDB
 * @returns {Promise<void>}
 */
async function connectToMongoDB() {
  try {
    if (!process.env.MONGODB_URI) {
      throw new Error(
        "❌ MONGODB_URI not found in .env file. Please add your MongoDB connection string."
      );
    }

    // Mongoose v6+ enables the new URL parser and unified topology by default.
    // Try the provided URI first. If the driver fails on SRV lookups (common
    // in restricted networks), attempt a fallback: resolve SRV records and
    // connect using a standard mongodb:// host list.
    try {
      const conn = await mongoose.connect(process.env.MONGODB_URI);
      console.log(`✅ MongoDB Connected: ${conn.connection.host}`);
      return conn;
    } catch (connErr) {
      console.error("❌ MongoDB Connection Error:", connErr.message);

      // If the error indicates SRV/DNS lookup was refused, attempt fallback
      if (connErr.message && connErr.message.includes("querySrv")) {
        try {
          // Extract host from the mongodb+srv URI
          const uri = process.env.MONGODB_URI;
          if (uri.startsWith("mongodb+srv://")) {
            const withoutPrefix = uri.replace("mongodb+srv://", "");
            const [authAndHost] = withoutPrefix.split("/");
            const atIndex = authAndHost.indexOf("@");
            let authPart = "";
            let hostPart = authAndHost;
            if (atIndex !== -1) {
              authPart = authAndHost.slice(0, atIndex + 1); // includes trailing @
              hostPart = authAndHost.slice(atIndex + 1);
            }

            const srvName = hostPart.split(":")[0];
            const records = await dns.resolveSrv(`_mongodb._tcp.${srvName}`);
            if (!records || records.length === 0) throw new Error("No SRV records found");

            const hosts = records.map(r => `${r.name}:${r.port || 27017}`).join(",");

            // Derive a replicaSet name by normalizing the first SRV target.
            const first = records[0].name.split(".")[0];
            const replicaSet = first.replace(/-shard-00-\d+$/, "-shard-0");

            const fallbackUri = `mongodb://${authPart}${hosts}/?replicaSet=${replicaSet}&authSource=admin&ssl=true&retryWrites=true&w=majority`;

            const conn2 = await mongoose.connect(fallbackUri);
            console.log(`✅ MongoDB Connected (fallback): ${conn2.connection.host}`);
            return conn2;
          }
        } catch (fallbackErr) {
          console.error("❌ MongoDB fallback error:", fallbackErr.message);
        }
      }

      // Re-throw to be handled by outer catch
      throw connErr;
    }

    console.log(`✅ MongoDB Connected: ${conn.connection.host}`);
    return conn;
  } catch (error) {
    console.error("❌ MongoDB Connection Error:", error.message);
    // Don't exit in development - allow file-based fallback
    if (process.env.NODE_ENV === "production") {
      process.exit(1);
    }
  }
}

/**
 * Disconnect from MongoDB
 * @returns {Promise<void>}
 */
async function disconnectFromMongoDB() {
  try {
    await mongoose.disconnect();
    console.log("✅ MongoDB Disconnected");
  } catch (error) {
    console.error("❌ Error disconnecting from MongoDB:", error.message);
  }
}

module.exports = {
  connectToMongoDB,
  disconnectFromMongoDB,
};
