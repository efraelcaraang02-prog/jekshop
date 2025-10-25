<?php
include('../db_connect.php');
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $region = $_POST['region'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, middle_name, last_name, birthday, age, sex, region, province, municipality, barangay, email, password) 
                VALUES ('$first_name', '$middle_name', '$last_name', '$birthday', '$age', '$sex', '$region', '$province', '$municipality', '$barangay', '$email', '$hashed_password')";
        if (mysqli_query($conn, $sql)) {
            $success = "Account created successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up | Caraang Aluminum Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #121212; color: #fff; font-family: 'Poppins', sans-serif; }
.signup-container { max-width: 500px; margin: 40px auto; padding: 30px; background: #f9f9f9; color: #222; border-radius: 15px; box-shadow:0 8px 20px rgba(0,0,0,0.3);}
h2 { text-align: center; margin-bottom: 20px; font-weight:700; }
.form-control { border-radius:8px; margin-bottom:15px; }
.btn-aluminum { background:#000; color:#fff; width:100%; border-radius:8px; transition:0.3s; }
.btn-aluminum:hover { background:#333; }
.alert { border-radius:8px; }
</style>
</head>
<body>

<div class="signup-container">
<h2>Create Account</h2>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
<?php if($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">
<input type="text" name="first_name" class="form-control" placeholder="First Name" required>
<input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
<input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
<input type="date" name="birthday" id="birthday" class="form-control" placeholder="Birthday" required>
<input type="text" name="age" id="age" class="form-control" placeholder="Age" readonly required>
<select name="sex" class="form-control" required>
    <option value="">Select Sex</option>
    <option>Male</option>
    <option>Female</option>
    <option>Other</option>
</select>

<!-- Address Dropdowns -->
<select id="region" name="region" class="form-control" required><option value="">Select Region</option></select>
<select id="province" name="province" class="form-control" required><option value="">Select Province</option></select>
<select id="municipality" name="municipality" class="form-control" required><option value="">Select Municipality</option></select>
<select id="barangay" name="barangay" class="form-control" required><option value="">Select Barangay</option></select>

<input type="email" name="email" class="form-control" placeholder="Email" required>
<input type="password" name="password" class="form-control" placeholder="Password" required>
<input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
<button type="submit" class="btn btn-aluminum">Sign Up</button>
<div class="text-center mt-3">
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</form>
</div>

<script>
// Load JSON files (must be served via localhost)
let regions = [], provinces = [], municipalities = [], barangays = [];

async function loadJSON() {
    regions = await fetch('../philippine-address-selector-main/ph-json/region.json').then(r => r.json());
    provinces = await fetch('../philippine-address-selector-main/ph-json/province.json').then(r => r.json());
    municipalities = await fetch('../philippine-address-selector-main/ph-json/city.json').then(r => r.json());
    barangays = await fetch('../philippine-address-selector-main/ph-json/barangay.json').then(r => r.json());
    populateRegions();
}

function populateRegions() {
    const regionSelect = document.getElementById('region');
    regions.forEach(r => {
        let o = document.createElement('option');
        o.value = r.region_code;
        o.textContent = r.region_name;
        regionSelect.appendChild(o);
    });
}

function populateProvinces(regionCode){
    const provinceSelect = document.getElementById('province');
    provinceSelect.innerHTML = '<option value="">Select Province</option>';
    provinces.filter(p => p.region_code === regionCode).forEach(p=>{
        let o=document.createElement('option'); o.value=p.province_code; o.textContent=p.province_name; provinceSelect.appendChild(o);
    });
    document.getElementById('municipality').innerHTML = '<option value="">Select Municipality</option>';
    document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
}

function populateMunicipalities(provinceCode){
    const municipalitySelect = document.getElementById('municipality');
    municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
    municipalities.filter(m => m.province_code === provinceCode).forEach(m=>{
        let o=document.createElement('option'); o.value=m.city_code; o.textContent=m.city_name; municipalitySelect.appendChild(o);
    });
    document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
}

function populateBarangays(cityCode){
    const barangaySelect = document.getElementById('barangay');
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    barangays.filter(b => b.city_code === cityCode).forEach(b=>{
        let o=document.createElement('option'); o.value=b.brgy_code; o.textContent=b.brgy_name; barangaySelect.appendChild(o);
    });
}

// Dropdown event listeners
document.getElementById('region').addEventListener('change', e=>populateProvinces(e.target.value));
document.getElementById('province').addEventListener('change', e=>populateMunicipalities(e.target.value));
document.getElementById('municipality').addEventListener('change', e=>populateBarangays(e.target.value));

// Auto-calculate age
document.getElementById('birthday').addEventListener('change', function(){
    const birthDate = new Date(this.value);
    const ageDifMs = Date.now() - birthDate.getTime();
    const ageDt = new Date(ageDifMs);
    document.getElementById('age').value = Math.abs(ageDt.getUTCFullYear() - 1970);
});

// Load JSON on page load
loadJSON();
</script>

</body>
</html>
