let mongoUrl = "mongodb+srv://admin:Anju@0112@cluster0.vwnxf.mongodb.net/?appName=Cluster0"
import mongoose from "mongoose"

mongoose.connect(mongourl)

let connection = mongoose.connection

connection.on('error', ()=>{
    console.log("MongoDB connection failed")
})