<?php
include_once '../config.php';
include_once '../helpers/helpers.php';

$pageTitle = "Request an Appointment";
ob_start();

// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isAuthorized()) {
    flash('error', ' you cant access that page. login please!');
    header("Location: " . asset('auth/login.php'));
    exit();
}

$user_id = $_SESSION['USER']['user_id'];
$user = fetchUser($user_id);

$user_diseases = fetchUserDiseases($user_id);
$bprocesses = fetchBloodProcesses();

$user_arr = array_map('ucwords', array_column($user_diseases, 'disease_name'));
?>
<?php if (!isset($_GET['step'])) : ?>
    <div class="wow animate__animated  animate__fadeIn container mt-lg-3" style="margin-top:80px ">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Confirm Your Data</h5>
            </div>
            <div class="card-body p-0">
                <div class="wsus__check_form ">
                    <div class="col-12 mb-2">
                        <input type="text" class="form-control" placeholder="Username" value="<?= $user['username'] ?>" readonly />
                    </div>
                    <div class="col-12 mb-2">
                        <input type="text" class="form-control" placeholder="Blood Group" value="<?= $user['group_name'] ?>" readonly />
                    </div>
                    <div class="col-12 mb-2">
                        <input type="text" class="form-control" placeholder="City" value="<?= $user['city'] ?>" readonly />
                    </div>
                    <div class="col-12 mb-2">
                        <input type="text" class="form-control" placeholder="Phone Number" value="<?= $user['phone_number'] ?>" readonly />
                    </div>
                    <div class="col-12 mb-4">
                        <input type="text" class="form-control" placeholder="No Diseases" value="<?= implode(', ', $user_arr); ?>" readonly />
                    </div>
                    <div class="modal-footer">
                        <a href="<?= asset('profile/index.php?id=' . $_SESSION['USER']['user_id'] . '&edit=true') ?>" class="btn btn-success rounded-pill">Edit</a>
                        <a href="<?= asset('actions/index.php?step=2'); ?>" class="btn btn-danger rounded-pill">Next</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php elseif (isset($_GET['step']) && $_GET['step'] == 2) : ?>
    <div class="wow animate__animated  animate__fadeIn container mt-lg-3" style="margin-top:80px ">
        <div class="card ">
            <div class="card-header">
                <h5 class="card-title header_title">Request an Appointment</h5>
            </div>
            <div class="card-body p-0">
                <div class="wsus__check_form ">
                    <form class="row w-100" action="action.php" method="POST">
                        <input type="hidden" id="user_id" name="user_id" value="<?= $user['user_id'] ?>">
                        <input type="hidden" id="birthday" name="birthday" value="<?= $user['birthday'] ?>">
                        <div class="col-xl-12 col-md-12 mb-3 ">
                            <select id="Select" class="mw-100" data-search="true" data-silent-initial-value-set="true" name="bprocess" placeholder="Blood Processes *" required>
                                <option value="" disabled selected>Blood Process *</option>
                                <?php foreach ($bprocesses as $bprocess) : ?>
                                    <option value="<?= $bprocess['process_id'] ?>"> <?= ucwords($bprocess['process_name']) ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xl-12 col-md-12 mb-3 ">
                            <select id="Select" class="mw-100" data-search="true" data-silent-initial-value-set="true" name="type" placeholder="Type *" required>
                                <option value="" disabled selected>Type *</option>
                                <option value="donation">Donate</option>
                                <option value="request">Receive</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <input type="number" class="form-control" placeholder="Blood Unit (ML) *" name="bunit" required />
                        </div>

                        <div class="mb-3">
                            <textarea class="form-control" id="case_details" rows="3" name="case_details" placeholder="Casem Details"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger rounded-pill">Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include('../layout.php');
?>