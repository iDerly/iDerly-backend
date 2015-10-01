<?php
/*! \file

### View elder profile

```
REQUEST /elder/view/[s:device_id]
```

#### Parameters
- `device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


#### Todo
- incorporate game result here (some stats like when game is last played, average score, hi score in each mode)
*/
$this->respond('/[s:device_id]', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id_from_device_id = $app->user_id_from_device_id;
    $device_id = $mysqli->escape_string($request->param('device_id'));

    // error checking
    if (is_empty(trim($device_id)))
        $service->flash("Please enter your device_id.", 'error');

    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $sql_query = "SELECT `user_id`, `device_id`, `date_created`, `name`, `attachment` FROM `user` WHERE `device_id` = ?";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("s", $device_id);
            $res = $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id, $device_id, $date_created, $name, $attachment);
            $stmt->fetch();

            $return_msg = array(
                "user_id" => $user_id,
                "device_id" => $device_id,
                "date_created" => $date_created,
                "name" => $name,
                "attachment" => $attachment,
                "game_hiscore" => $game['hiscore'],
                "game_hiscore_classic" => $game['hiscore_classic'],
                "game_hiscore_unlimited" => $game['hiscore_unlimited'],
                "game_lastplayed_classic" => $game['lastplayed_classic'],
                "game_lastplayed_unlimited" => $game['lastplayed_unlimited'],
                "game_avgscore" => $game['avgscore']
            );

            $stmt->close();
            $return['status'] = 0;
            $return['message'] = $return_msg;

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
