<?php
/*! \file

### Add game_result

```
POST /game/add_result
```

#### Parameters
- `device_id`
- `score`
- `time_start` YYYY-MM-DD HH:mm:SS; `Y-M-D H:i:s`; 
- `time_end` YYYY-MM-DD HH:mm:SS; `Y-M-D H:i:s`; 
- `mode`: "classic" or "unlimited"

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id_from_device_id = $app->user_id_from_device_id;
    $device_id = $mysqli->escape_string($request->param('device_id'));
    $score = $mysqli->escape_string($request->param('score'));
    $time_start = $mysqli->escape_string($request->param('time_start'));
    $time_end = $mysqli->escape_string($request->param('time_end'));
    $mode = $mysqli->escape_string($request->param('mode'));

    // error checking
    if (is_empty(trim($device_id)))    $service->flash("Please enter your device_id.", 'error');
    if (is_empty(trim($score)))      $service->flash("Please enter your score.", 'error');
    if (is_empty(trim($mode)))      $service->flash("Please enter your mode.", 'error');
    
    if (is_empty(trim($time_start)))  $service->flash("Please enter the date and time of game start.", 'error');
    if (($timestamp_start = strtotime($time_start)) === false)
                                     $service->flash("Please enter a valid date and time for game end.", 'error');
    $time_start = date("Y-m-d H:i:s", $timestamp_start);

    if (is_empty(trim($time_end)))  $service->flash("Please enter the date and time of game end.", 'error');
    if (($timestamp_end = strtotime($time_end)) === false)
                                     $service->flash("Please enter a valid date and time for game end.", 'error');
    $time_end = date("Y-m-d H:i:s", $timestamp_end);



    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        // get user_id from device_id
        $user_id = $user_id_from_device_id($mysqli, $device_id);


        $sql_query = "INSERT INTO `game_result`(`time_start`, `time_end`, `score`, `user_id`, `mode`)
                      VALUES(?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("ssiis", $time_start, $time_end, $score, $user_id, $mode);
            $res = $stmt->execute();
            if ($res) {
                $service->flash("Game result successfully stored.", 'success');
                $return['status'] = 0;
                $return['message'] = $service->flashes('success');
            } else {
                $service->flash("Failed to insert data to database: " . $stmt->error, 'error');
                $return['status'] = -1;
                $return['message'] = $service->flashes('error');
            }
            $stmt->close();
        } else {
            $service->flash("SQL statement error ", 'error');
            $return['status'] = -1;
            $return['message'] = $service->flashes('error');
        }
    } else {
        $return['status'] = -1;
        $return['message'] = $error_msg;
    }
    return json_encode($return);
});
