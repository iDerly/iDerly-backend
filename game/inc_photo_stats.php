<?php
/*! \file

### Increase photo statistics

```
POST /game/inc_photo_stats
```

#### Parameters
- `photo_id`
- `option`:
  - 0: increment number of appearance ONLY
  - 1: increment number of appearance AND correctness

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id_from_device_id = $app->user_id_from_device_id;

    $photo_id = $mysqli->escape_string($request->param('photo_id'));
    $option = intval($mysqli->escape_string($request->param('option')));

    // error checking
    if (is_empty(trim($photo_id)))    $service->flash("Please enter your photo_id.", 'error');
    if (is_empty(trim($option)) || $option < 0 || $option > 1)    $service->flash("Please enter the option: 0: increment number of appearance ONLY, 1: increment number of appearance AND correctness.", 'error');


    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $sql_query = "SELECT `appear`, `correct` FROM `photo` WHERE `id` = ?";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("i", $photo_id);
            $res = $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($appear, $correct);
            $stmt->fetch();

            $appear++;
            if ($option === 1) {
                $correct++;
            }


            $sql_query = "UPDATE `photo` SET `appear` = ?, `correct` = ? WHERE `id` = ?";
            $stmt = $mysqli->prepare($sql_query);
            if ($stmt) {
                $stmt->bind_param("iii", $appear, $correct, $photo_id);
                $res = $stmt->execute();
                if ($res) {
                    $service->flash("Photo statistics successfully updated.", 'success');
                    $return['status'] = 0;
                    $return['message'] = $service->flashes('success');
                } else {
                    $service->flash("Failed to insert data to database: " . $stmt->error, 'error');
                    $return['status'] = -1;
                    $return['message'] = $service->flashes('error');
                }
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
