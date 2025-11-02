<?php include 'include/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<!-- <link rel="stylesheet" href="css/app-Da4JohBc.css"> -->
<style>
  .hidden {
    display: none !important;
}
.gap-4 {
  gap: 1rem;
}
.items-center {
  align-items: center;
}
.flex-col {
  flex-direction: column;
}
.flex {
  display: flex;
}
.gap-3 {
    gap: .75rem;
}

.flex-wrap {
    flex-wrap: wrap;
}
.cursor-pointer {
  cursor: pointer;
}
.transition {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(.4, 0, .2, 1);
  transition-duration: .15s;
}
.border-dashed {
  border-style: dashed;
}
.border-2 {
  border-width: 2px;
}
.rounded-full {
  border-radius: 9999px;
}
.overflow-hidden {
  overflow: hidden;
}
.justify-center {
  justify-content: center;
}
.items-center {
  align-items: center;
}
.w-32 {
  width: 8rem;
}
.h-32 {
  height: 8rem;
}
.flex {
  display: flex;
}
.object-cover {
  -o-object-fit: cover;
  object-fit: cover;
}
.w-full {
  width: 100%;
}
.h-full {
  height: 100%;
}
img, video {
  max-width: 100%;
  height: auto;
}
img, svg, video, canvas, audio, iframe, embed, object {
  display: block;
}
[type=file] {
  background: unset;
  border-color: inherit;
  border-width: 0;
  border-radius: 0;
  padding: 0;
  font-size: unset;
  line-height: inherit;
}
.py-2 {
    padding-top: .5rem;
    padding-bottom: .5rem;
}
.px-3 {
    padding-left: .75rem;
    padding-right: .75rem;
}
.bg-gray-100 {
    --tw-bg-opacity: 1;
    background-color: rgb(243 244 246 / var(--tw-bg-opacity, 1));
}
.rounded-lg {
    border-radius: .5rem;
}
.gap-2 {
    gap: .5rem;
}
.text-green-600 {
    --tw-text-opacity: 1;
    color: rgb(22 163 74 / var(--tw-text-opacity, 1));
}
.w-5 {
    width: 1.25rem;
}
.h-5 {
    height: 1.25rem;
}
[type=checkbox] {
    border-radius: 0;
}
.hover\:bg-green-50:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(240 253 244 / var(--tw-bg-opacity, 1));
}
@media (min-width: 640px) {
    .sm\:w-auto {
        width: auto;
    }
}
/* [type=checkbox], [type=radio] {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding: 0;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    display: inline-block;
    vertical-align: middle;
    background-origin: border-box;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    flex-shrink: 0;
    height: 1rem;
    width: 1rem;
    color: #2563eb;
    background-color: #fff;
    border-color: #6b7280;
    border-width: 1px;
    --tw-shadow: 0 0 #0000;
} */
button, input, optgroup, select, textarea {
    font-family: inherit;
    font-feature-settings: inherit;
    font-variation-settings: inherit;
    font-size: 100%;
    font-weight: inherit;
    line-height: inherit;
    letter-spacing: inherit;
    color: inherit;
    margin: 0;
    padding: 0;
}
</style>
<body class="sidebar-dark">
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php include 'includes/navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <?php include 'includes/settings.php'; ?>
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <?php include 'includes/sidebar.php'; ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Set your Profile</h3>
                  <h6 class="font-weight-normal mb-0">Set Profile as a <?php echo $role; ?> on <?php echo $settings['site_name'];?></h6>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <?php
                if(isset($_SESSION['error1'])){
                  echo "
                    <div class='alert alert-danger alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-close'></i>Error!</h4>
                      ".$_SESSION['error1']."
                    </div>
                  ";
                  unset($_SESSION['error1']);
                }
                if(isset($_SESSION['success'])){
                  echo "
                    <div class='alert alert-success alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-check'></i> Success!</h4>
                      ".$_SESSION['success']."
                    </div>
                  ";
                  unset($_SESSION['success']);
                }
                if(isset($_SESSION['cancelled'])){
                  echo "
                    <div class='alert alert-secondary alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-delete-forever'></i> Cancelled!</h4>
                      ".$_SESSION['cancelled']."
                    </div>
                  ";
                  unset($_SESSION['cancelled']);
                }
              ?>
            </div>
          </div>
          <div class="row">
            <?php if($user['profile_set'] == 0): ?>
              <?php if($user['role'] == 'user'): ?>
                <div class="col-md-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Add Player Profile</h4>

                      <form class="form" action="set-profile-action" method="POST" enctype="multipart/form-data">

                        <div>
                          <h5 class="text-xl font-semibold text-gray-700 mb-4">Player Profile Image</h5>
                          <div class="flex flex-col items-center gap-4">
                              <label for="profile_image" class="cursor-pointer">
                                  <div class="w-32 h-32 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 hover:border-green-400 transition">
                                      <img id="image_preview" src="images/profile.jpg" alt="Preview" class="w-full h-full object-cover">
                                  </div>
                              </label>
                              <input type="file" id="profile_image" name="profile_image" accept="image/*" class="hidden">
                              <p class="text-sm text-gray-500">Click the circle to upload an image (JPG, PNG, max 2MB)</p>
                          </div>
                        </div>

                        <hr>

                        <div>
                            <h5 class="text-xl font-semibold text-gray-700 mb-4">1. Game Information</h5>
                            <div class="row">

                              <div class="col-6">
                                <div class="form-group">
                                    <label>Game Type</label>
                                    <select id="game_type" name="game_type" class="form-control" required>
                                        <option value="" disabled selected>-- Select Game Type --</option>
                                        <option value="football" >⚽ Football</option>
                                        <option value="basketball" >🏀 Basketball</option>
                                    </select>
                                </div>

                                
                                <div class="form-group">
                                    <label>Nationality</label>
                                    <select id="countrySelect" name="country" class="form-control" required>
                                        <option value="">-- Select Country --</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <?php 
                                    // max age is 21 years
                                    $maxDate = date('Y-m-d', strtotime('-16 years'));
                                    ?>
                                    <input type="date" name="dob" 
                                        class="form-control" 
                                        placeholder="e.g., AIK Fotboll, LA Lakers" 
                                        value="" max="<?php echo $maxDate; ?>" required>
                                </div>

                              </div>
                              <div class="col-6">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="" disabled selected>-- Select Gender --</option>
                                        <option value="male" >Male</option>
                                        <option value="female" >Female</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Club / Team</label>
                                    <input type="text" name="club" 
                                        class="form-control" 
                                        placeholder="e.g., AIK Fotboll, LA Lakers" 
                                        value="" required>
                                </div>
                              </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h5 class="text-xl font-semibold text-gray-700 mb-4">2. Positions</h5>

                            <div id="football_positions" class="flex flex-wrap gap-3 hidden">
                  
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Striker"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">ST (Striker)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Central Midfielder"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">CM (Central Midfielder)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Defensive Midfielder"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">DM (Defensive Midfielder)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Center Back"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">CB (Center Back)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Left Winger"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">LW (Left Winger)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Attacking Midfielder"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">AM (Attacking Midfielder)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Right Winger"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">RW (Right Winger)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Right Back"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">RB (Right Back)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Left Back"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">LB (Left Back)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Goalkeeper"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">GK (Goalkeeper)</span>
                              </label>
                            </div>

              
                            <div id="basketball_positions" class="flex flex-wrap gap-3 hidden">
                  
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Point Guard"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">PG (Point Guard)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Shooting Guard"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">SG (Shooting Guard)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Small Forward"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">SF (Small Forward)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Power Forward"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">PF (Power Forward)</span>
                              </label>
                              <label class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg cursor-pointer hover:bg-green-50 transition w-full sm:w-auto">
                                  <input type="checkbox" name="positions[]" value="Center"  class="h-5 w-5 text-green-600">
                                  <span class="text-gray-700 text-sm sm:text-base">C (Center)</span>
                              </label>
                            </div>

                        </div>

                        <div class="row">
                          <div class="grid col-6 gap-6">
                              <div class="form-group">
                                  <label>Player Weight (kg)</label>
                                  <input type="number" name="weight" value="" min="1" max="1000" class="form-control" placeholder="e.g., 75" required>
                              </div>
                              <div class="form-group">
                                  <label>Player Description</label>
                                  <textarea name="description" class="form-control"></textarea>
                              </div>
                          </div>

                          <div class="grid col-6 gap-6">
                              <div class="form-group">
                                  <label>Height (meters)</label>
                                  <input type="number" name="height" value="" min="1" max="1000" class="form-control">
                              </div>
                              <div id="footedness_field" class="hidden">
                                  <label>Footedness</label>
                                  <select name="footedness" class="form-control">
                                      <option value="" disabled selected>-- Select Footedness --</option>
                                      <option value="Right" >Right Footed</option>
                                      <option value="Left" >Left Footed</option>
                                      <option value="Both" >Both Footed</option>
                                  </select>
                              </div>
                          </div>
                        </div>

                        <div>
                            <label>Academy Player?</label>
                            <select id="academy_status" name="academy_status" class="form-control" required>
                                <option value="" disabled selected>-- Select --</option>
                                <option value="yes">Yes, Academy Player</option>
                                <option value="no">No, Non-Academy Player</option>
                            </select>
                            <div id="academy_name_field" class="mt-4 hidden">
                                <label>Academy Name</label>
                                <input type="text" name="academy_name" value="" class="form-control">
                            </div>
                        </div>

                        <button type="submit" name="save" class="btn btn-primary btn-rounded btn-icon-text mb-4 mt-4">
                          <i class="ti-save btn-icon-prepend"></i>
                          Save
                        </button>
                      </form>

                    </div>
                  </div>
                </div>
              <?php elseif($user['role'] == 'agent'): ?>
                <div class="col-md-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Add Agent/Scout Profile</h4>

                      <form class="form" action="set-profile-action" method="POST" enctype="multipart/form-data">

                        <div>
                          <h5 class="text-xl font-semibold text-gray-700 mb-4">Agent/Scout Logo</h5>
                          <div class="flex flex-col items-center gap-4">
                              <label for="profile_image" class="cursor-pointer">
                                  <div class="w-32 h-32 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 hover:border-green-400 transition">
                                      <img id="image_preview" src="images/profile.jpg" alt="Preview" class="w-full h-full object-cover">
                                  </div>
                              </label>
                              <input type="file" id="profile_image" name="profile_image" accept="image/*" class="hidden">
                              <p class="text-sm text-gray-500">Click the circle to upload an image (JPG, PNG, max 2MB)</p>
                          </div>
                        </div>

                        <hr>

                        <div>
                            <h5 class="text-xl font-semibold text-gray-700 mb-4">Create Agent/Scout Profile</h5>
                            <div class="row">

                              <div class="col-6">
                                <div class="form-group">
                                    <label>Organization or Club Name</label>
                                    <input type="text" name="organization" 
                                        class="form-control" 
                                        placeholder="e.g., AIK Fotboll, LA Lakers" 
                                        value="" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Nationality</label>
                                    <select id="countrySelect" name="country" class="form-control" required>
                                        <option value="">-- Select Country --</option>
                                    </select>
                                </div>

                              </div>
                              <div class="col-6">

                                <div class="form-group">
                                    <label>License Number</label>
                                    <input type="text" name="license_number" 
                                        class="form-control" 
                                        placeholder="928940594" 
                                        value="" required>
                                </div>

                                <div class="form-group">
                                    <label>Game Type</label>
                                    <select id="game_type" name="game_type" class="form-control" required>
                                        <option value="" disabled selected>-- Select Game Type --</option>
                                        <option value="football" >⚽ Football</option>
                                        <option value="basketball" >🏀 Basketball</option>
                                    </select>
                                </div>
                              </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                          <div class="grid col-12 gap-6">
                              <div class="form-group">
                                  <label>Bio</label>
                                  <textarea name="bio" rows="4" class="form-control"></textarea>
                              </div>
                          </div>
                        </div>

                        <button type="submit" name="save" class="btn btn-primary btn-rounded btn-icon-text mb-4 mt-4">
                          <i class="ti-save btn-icon-prepend"></i>
                          Save
                        </button>
                      </form>

                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class='alert alert-success alert-dismissible mb-0'>
                          <h4><i class='icon mdi mdi-check'></i> Success!</h4>
                          Profile has already been set
                        </div>
                    </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <?php include 'includes/footer.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <?php include 'includes/scripts.php'; ?>
  <script src="../js/countries.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        let select = document.getElementById("countrySelect");
        let oldCountry = "";
        country_arr.forEach(function (country) {
            let option = document.createElement("option");
            option.value = country;
            option.text = country;
            if (oldCountry === country) option.selected = true;
            select.appendChild(option);
        });
    });
  </script>
  <script>
    document.getElementById("profile_image").addEventListener("change", e => {
        const [file] = e.target.files;
        if (file) document.getElementById("image_preview").src = URL.createObjectURL(file);
    });
    document.getElementById('game_type').addEventListener('change', function() {
        document.getElementById('football_positions').classList.add('hidden');
        document.getElementById('basketball_positions').classList.add('hidden');
        document.getElementById('footedness_field').classList.add('hidden');
        if (this.value === 'football') {
            document.getElementById('football_positions').classList.remove('hidden');
            document.getElementById('footedness_field').classList.remove('hidden');
        } else if (this.value === 'basketball') {
            document.getElementById('basketball_positions').classList.remove('hidden');
        }
    });
    document.getElementById('academy_status').addEventListener('change', function() {
        document.getElementById('academy_name_field').classList.toggle('hidden', this.value !== 'yes');
    });
</script>
</body>

</html>
