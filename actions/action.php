<?php
include '../config.php';
include '../helpers/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $conn;
    try {
        $id = $_POST['user_id'];
        $birth = $_POST['birthday'];
        $bunit = $_POST['bunit'];
        $bprocess = $_POST['bprocess'];
        $type = $_POST['type'];
        $case_details = $_POST['case_details'] ?? NULL;

        $user = fetchUser($id);
        $process_name = fetchBloodProcess($bprocess);
        $bprocesses = fetchBloodProcesses();

        // Check if female and trying to donate platelets
        if (strtolower($user['gender']) == 'female' && $type == 'donation' && strtolower($process_name) == 'platelet') {
            flash('error', 'Females cannot donate platelets.');
            redirectBack();
        }

        if ($type == 'donation') {
            // Check if user can donate based on blood group and process
            $bprocesses_ = array_filter($bprocesses, function ($bprocess) {
                global $user;
                if (strtolower($bprocess['process_name']) == 'whole blood') {
                    return true;
                } elseif (strtolower($bprocess['process_name']) == 'power red') {
                    if (in_array($user['group_name'], ['A-', 'B-', 'O+', 'O-'])) {
                        return true;
                    }
                } elseif (strtolower($bprocess['process_name']) == 'platelet') {
                    if (in_array($user['group_name'], ['A-', 'AB-', 'A+'])) {
                        return true;
                    }
                }
            });

            if (!in_array($bprocess, array_column($bprocesses_, 'process_id'))) {
                flash('error', 'You cannot donate ' . $process_name . ' with your blood group (' . $user['group_name'] . ')');
                redirectBack();
            }

            // Validate age for donation
            $age = ceil(checkDate2($birth) / 365);
            if ($age < 18) {
                flash('error', 'You must be at least 18 years old to donate.');
                redirectBack();
            }

            // Check if user can donate based on previous donations
            $canDonate = canUserDonate($id, $bprocess, $bunit);
            if (!$canDonate[0]) {
                flash('error', $canDonate[1]);
                redirectBack();
            }

            // Create new donation appointment
            createNewAppointment($id, $bprocess, $type, $bunit, $case_details);
            flash('success', 'You have requested a blood donation appointment. You will be notified once it gets approved.');
            redirectBack();
        } else {
            // Check if user can receive based on previous appointments
            $canReceive = canUserReceive($id);
            if (!$canReceive[0]) {
                flash('error', $canReceive[1]);
                redirectBack();
            }

            // Create new request appointment
            createNewAppointment($id, $bprocess, $type, $bunit, $case_details);
            flash('success', 'You have requested a blood receival appointment. You will be notified once it gets approved.');
            redirectBack();
        }
    } catch (Exception $e) {
        flash('error', 'You are not authorized to access this page.');
        redirectBack();
    }
}
