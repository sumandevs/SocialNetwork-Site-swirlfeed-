
<?php

$row['friend_array'] = ",Sanjay,Sourav,Avishek,Ajay,Utpol,Somrat,Supriyo,"; //string    friend array of suman

$row['friend_array'] = ",Sanjay,Kingshuk,Bijoy,Avishek,Rohit,Ajay,Bipul,Supriyo,Akram,"; //string    friend array of Seth

// mutual friends = Avishek,Ajay,Supriyo

$suman_not_friend_array = array();  // [,"Kingshuk","Bijoy","Rohit","Bipul","Akram"]
$seth_not_friend = array();  //  ["Sourav","Utpol","Somrat"]

$sumans_friend_array = explode($row['friend_array'], ',');      //    ["","Sanjay",Sourav","Avishek","Ajay","Utpol","Somrat"]  => suman's friend array
$seths_friend = explode($row['friend_array'], ',');      //    ["","Sanjay","Kingshuk","Bijoy","Avishek","Rohit","Ajay","Bipul","Supriyo","Akram"]  => suman's friend array


foreach($seths_friend as $i){   // $i = sanjay   (for the first loop)

    if($i !== "") {
        if(!in_array($i, $sumans_friend_array)){
            array_push($suman_not_friend_array, $i);
        }
    }

}






    













?>