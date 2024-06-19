<?php
include '../config.php';
include '../helpers/helpers.php';
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
ob_start();
$pageTitle = "Home";
$_SESSION['LAYOUT_PATH_FRONTEND'] = '/bloodX/';
$_SESSION['LAYOUT_PATH'] = '/admin/';
$_SESSION['DEFAULT_IMAGE_PATH'] = 'default.png';
$_SESSION['BANNER_SLIDER_IMAGES'] = ['banner1.gif', 'banner2.png', 'banner3.png'];
$_SESSION['PER_PAGE'] = 5;

$blood_groups = fetchBloodGroups();
list($donors, $recipients, $waiting_list, $requests) = fetchCountingHomeData();

$groups_counted_waiting_list =  countBloodGroups($waiting_list);
$groups_counted_requests =  countBloodGroups($requests);

?>
<section class="section">
  <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-danger">
          <i class="fas fa-user"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <a class="nav-link" href="<?= asset('admin/donors/index.php') ?>">
              <h4>Donors</h4>
            </a>
          </div>
          <div class="card-body">
            <?= count($donors) ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-danger">
          <i class="fas fa-user"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <a class="nav-link" href="<?= asset('admin/recipients/index.php') ?>">
              <h4>Recipients</h4>
            </a>
          </div>
          <div class=" card-body">
            <?= count($recipients) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h4>Waiting List (<?= count($waiting_list); ?>)</h4>
    </div>
    <div class="row">
      <?php foreach ($blood_groups as $group) : ?>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
              <i class="fas fa-tint"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <a class="nav-link" href="<?= asset('admin/waiting-list/index.php?group=' . $group['group_id'] . '&filter_by=group') ?>">
                  <h4><?= $group['group_name'] ?></h4>
                </a>
              </div>
              <div class="card-body">
                <?= $groups_counted_waiting_list[$group['group_name']] ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h4>Requests (<?= count($requests); ?>)</h4>
    </div>
    <div class="row">
      <?php foreach ($blood_groups as $group) : ?>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
              <i class="fas fa-tint"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <a class="nav-link" href="<?= asset('admin/manage-requests/index.php?group=' . $group['group_id'] . '&filter_by=group') ?>">
                  <h4><?= $group['group_name'] ?> </h4>
                </a>
              </div>
              <div class="card-body">
                <?= $groups_counted_requests[$group['group_name']] ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include('layout.php');
?>