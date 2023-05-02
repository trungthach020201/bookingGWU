var mysql = require("mysql");
var con =  mysql.createConnection({
    host: "localhost",
    user: "root",
    password:"",
    database:"bookingroom"
});
var room = [];
async function insert(oldSchedule) {

    await con.connect(function(err){
        if (err) throw err;
        console.log("Connected");
    })
    await con.query("SELECT * FROM room", function (err, result, fields) {
        if (err) throw err;
        // console.log(result);

        // console.log(oldSchedule);

        const schedule = oldSchedule.map(([date, slot, roomName, cls]) => [date, slot, findIdByRoomName(result, roomName), cls]);

        // console.log(schedule);

        con.query("TRUNCATE TABLE schedule", function (err, result, fields) {
            if (err) throw err;
            console.log("Number of records deleted: " + result.affectedRows);

            con.query("INSERT INTO `schedule`(`date` ,`slot`, `room_id`, `class`) VALUES ?", [schedule], function (err, result, fields) {
                if (err) throw err;
                console.log("Number of records inserted: " + result.affectedRows);

                con.end();
            });
        });

    });

};
const findIdByRoomName = (data, roomName) => {
    const foundObject = data.find(object => object.name === roomName);
    return foundObject ? foundObject.id : null;
};

module.exports = {
	insert
};