<?PHP

//get the checking date from the form
//get the checkout date from the form 
//get the number of adults
//get the number of children
//get the room type
//check the availability of the room -> Redirect
//if booked, mark as booked. -> Redirect
//if error, Redirect (Error Reserving your Room)

//Database
//Table Room (RoomID, NoOFBeds)
//Table Reservations(RoomID, BookedBy, CheckInDate, CheckOutDate, 

//Calculating the availability
//get the rooms available by the beds
//if the rooms available, check the reservation table
//if the checkOut date is > than the new CHeckIN date, That room is not available. 
// || if the new CheckoutDate is larger than the Previous CheckIn Date, Room is not available.
// If the room is available, Save the user Name and the Dates,
// If not, Check the next room available. 
// Continue until finds the available room or there is no rooms
// If there is no rooms, Redirect(No rooms available)
// If Reservation Successful, Redirect(Reservation Success. Your room ID is $RoomID)
function getIfSet(&$value, $def = null)
{
    return isset($value) ? $value : $def;
}

$host = "localhost";
$userdb = "root";
$pswdb = "";
$db = "hoteldb";

//variables
$CheckInDate = "2020-09-12";
$CheckOutDate = "2020-09-14";
$Adults = "1";
$Children = "1";
$Type = "JuniorSuites";
$Beds = "1";
$BedType = "Double";
$user = "pasananuththara19@gmail.com";

// Connecting to the database
$con = mysqli_connect($host, $userdb, $pswdb, $db);

// If connection error
if ($con) {
    // Connection Success
    // echo "SS";

    // Select All the rooms matching
    $matchingRooms = "SELECT * FROM Room WHERE NoOfBeds = '$Beds' AND BedType = '$BedType' AND RoomType = '$Type'";
    $QRYmatchingRooms = mysqli_query($con, $matchingRooms);

    if ($QRYmatchingRooms) {
        // Get the number of rooms matching (Rows Count)
        $countMatchingRooms = mysqli_num_rows($QRYmatchingRooms);

        echo "$countMatchingRooms"; // Just to debug;

        // if the number of rows are larger than 0 (Means available)
        if ($countMatchingRooms > 0) {
            echo " Rooms Available"; // Debug
            // Get the room IDS

            for ($r = 0; $r < $countMatchingRooms; $r++) {
                $fetchedData  = mysqli_fetch_array($QRYmatchingRooms);

                echo "<br /> Available Room ID " . $fetchedData['RoomID']; // To debug

                // Check the available room for reserved status
                $reserveCheck = "SELECT * FROM Reservations WHERE RoomID = " . $fetchedData['RoomID'];
                $QRYreserveCheck = mysqli_query($con, $reserveCheck);

                if ($QRYreserveCheck) // If it is valid
                {
                    $fetchReserved = mysqli_fetch_array($QRYreserveCheck);
                    // It is reserved 

                    if ($fetchReserved == null) {
                        echo " Is Fully Available";
                        // If it is fully available Book it. 
                        
                        $bookIt = "INSERT INTO Reservations VALUES('".$fetchedData['RoomID']."', '$user', '$CheckInDate', '$CheckOutDate', '$Adults', '$Children')";
                        $QryBookNow = mysqli_query($con, $bookIt); // initialize the quary

                        if($QryBookNow){ // IF success
                            // Booking Success
                            echo "<br>Successfully Booked Room ". $fetchedData['RoomID'];
                        }
                    break;
                    }
                    else{
                        // If it is not fully available, Check for the dates 
                        echo " (Indexed in Reserved)";
                        
                        // Select the room Id whichs CheckOutDate is <  NewCheckInDate And CheckInDate <  newCheckOutDate (If the room is booked by multiple)

                        $checkDates = "SELECT * FROM Reservations WHERE CheckOutDate < '$CheckInDate' AND CheckInDate < '$CheckOutDate' AND RoomID = '".$fetchedData['RoomID']."'";
                        $QRYCheckDates = mysqli_query($con, $checkDates);

                        if($QRYCheckDates) // If no errors
                        {
                            // This gets the Room Count
                            $CountAvalableByDate = mysqli_num_rows($QRYCheckDates);

                            // If the Count is larger than 0, That means, the room is available.
                            if($CountAvalableByDate > 0)
                                echo "Room is available for your time period"; // Debug
                        }

                    }
                }
            }
        } else {
            // Rooms Not Available
        }
    }
}