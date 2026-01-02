<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['parent_id'])){
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['parent_id'];

// ---------- AJAX HANDLER ----------
if(isset($_GET['action'])){
    header('Content-Type: application/json');
    $action = $_GET['action'];

    if($action == 'get_vaccines'){
        $hospital_id = isset($_GET['hospital_id']) ? intval($_GET['hospital_id']) : null;
        if($hospital_id){
            $stmt = $conn->prepare("SELECT v.vaccine_id,v.vaccine_name,v.description
                                    FROM vaccine v
                                    INNER JOIN hospital_vaccine hv ON hv.vaccine_id=v.vaccine_id
                                    WHERE hv.hospital_id=? AND hv.availability_status='Available'");
            $stmt->execute([$hospital_id]);
        }else{
            $stmt = $conn->prepare("SELECT v.vaccine_id,v.vaccine_name,v.description
                                    FROM vaccine v
                                    INNER JOIN hospital_vaccine hv ON hv.vaccine_id=v.vaccine_id
                                    WHERE hv.availability_status='Available'
                                    GROUP BY v.vaccine_id");
            $stmt->execute();
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if($action == 'get_hospitals'){
        $vaccine_id = isset($_GET['vaccine_id']) ? intval($_GET['vaccine_id']) : null;
        if($vaccine_id){
            $stmt = $conn->prepare("SELECT h.hospital_id,h.hospital_name,h.address
                                    FROM hospital h
                                    INNER JOIN hospital_vaccine hv ON hv.hospital_id=h.hospital_id
                                    WHERE hv.vaccine_id=? AND hv.availability_status='Available'");
            $stmt->execute([$vaccine_id]);
        }else{
            $stmt = $conn->prepare("SELECT hospital_id,hospital_name,address FROM hospital");
            $stmt->execute();
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
}

// ---------- NORMAL DASHBOARD ----------
$stmt = $conn->prepare("SELECT * FROM parent WHERE parent_id=?");
$stmt->execute([$parent_id]);
$parent = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM child WHERE parent_id=?");
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent Dashboard | e-Vaccination</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
body{margin:0;font-family:'Inter',sans-serif;background:#f5f7fa;color:#0a2540;}
a{text-decoration:none;color:inherit;}
.container{max-width:1200px;margin:0 auto;padding:40px 20px;}
.hidden{display:none!important;}
.navbar{display:flex;justify-content:space-between;align-items:center;padding:20px;background:linear-gradient(135deg,#0a2540,#0e3a5d);color:#e6f1ff;}
.navbar a{margin-left:20px;font-weight:500;color:#e6f1ff;}
.profile-card{position:relative;background:linear-gradient(135deg,#22c1c3,#1fc8b8);border-radius:20px;padding:30px 40px;color:#fff;margin-bottom:30px;box-shadow:0 20px 40px rgba(0,0,0,0.2);}
.profile-card h2{font-size:28px;margin-bottom:10px;}
.profile-card p{opacity:0.9;margin-bottom:5px;}
.profile-card .edit-btn{position:absolute;top:20px;right:20px;background:white;color:#0a2540;padding:8px 12px;border-radius:8px;cursor:pointer;font-weight:600;transition:0.3s;}
.profile-card .edit-btn:hover{background:#e6f1ff;}
.child-section h2{margin-bottom:20px;}
.child-grid{display:flex;flex-wrap:wrap;gap:20px;}
.child-card{background:white;padding:20px;border-radius:16px;flex:1 1 200px;box-shadow:0 15px 35px rgba(0,0,0,0.08);position:relative;cursor:pointer;transition:0.3s;}
.child-card:hover{transform:translateY(-6px);}
.child-card h3{color:#0e3a5d;margin-bottom:8px;}
.child-card p{opacity:0.8;font-size:14px;}
.child-card .edit-child{position:absolute;top:10px;right:10px;background:#22c1c3;color:white;padding:5px 10px;border-radius:6px;font-size:12px;cursor:pointer;}
.child-card .edit-child:hover{background:#1fa9a9;}
.add-child{background:#0a2540;color:white;display:flex;align-items:center;justify-content:center;font-size:24px;}
.add-child:hover{background:#0e3a5d;}
.card-grid{display:flex;flex-wrap:wrap;gap:25px;margin-bottom:30px;}
.card{flex:1 1 250px;padding:30px 20px;border-radius:20px;text-align:center;color:white;cursor:pointer;position:relative;overflow:hidden;transition:0.3s;}
.card:hover{transform:translateY(-8px);box-shadow:0 20px 40px rgba(0,0,0,0.2);}
.card h3{margin-bottom:10px;}
.card p{opacity:0.9;font-size:14px;}
.card.vaccine{background:linear-gradient(135deg,#1fc8b8,#22c1c3);}
.card.hospital{background:linear-gradient(135deg,#0a2540,#0e3a5d);}
.card.action{background:linear-gradient(135deg,#22c1c3,#1fc8b8);}
.btn-appointment{display:inline-block;padding:10px 18px;border-radius:8px;background:#22c1c3;color:white;font-weight:600;margin-top:15px;}
.btn-appointment:hover{background:#1fa9a9;}
@media(max-width:768px){.card-grid,.child-grid{flex-direction:column;}.card{width:100%;}}
</style>
</head>
<body>

<div class="navbar">
    <span>e-Vaccination</span>
    <div>
        <a href="#">Dashboard</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="container">

<!-- Profile -->
<div class="profile-card">
    <h2><?= htmlspecialchars($parent['name']); ?></h2>
    <p>Email: <?= htmlspecialchars($parent['email']); ?></p>
    <p>Phone: <?= htmlspecialchars($parent['phone']); ?></p>
    <p>Address: <?= htmlspecialchars($parent['address']); ?></p>
</div>

<!-- Children -->
<div class="child-section">
<h2>My Children</h2>
<div class="child-grid">
<?php foreach($children as $child): ?>
<div class="child-card">
    <h3><?= htmlspecialchars($child['child_name']); ?></h3>
    <p>Gender: <?= $child['gender']; ?></p>
    <p>DOB: <?= $child['date_of_birth']; ?></p>
    <p>Blood Group: <?= $child['blood_group']; ?></p>
    <span class="edit-child" onclick="window.location.href='edit_child.php?child_id=<?= $child['child_id']; ?>'">Edit</span>
</div>
<?php endforeach; ?>
<div class="child-card add-child" onclick="window.location.href='add_child.php'"><span>+</span></div>
</div>
</div>

<!-- Actions -->
<h2>Actions</h2>
<div class="card-grid" id="initial-cards">
    <div class="card action" onclick="loadVaccines()">Vaccines</div>
    <div class="card action" onclick="loadHospitals()">Hospitals</div>
</div>

<div class="card-grid hidden" id="dynamic-cards"></div>

</div>

<script>
let dynamicCards = document.getElementById('dynamic-cards');
let initialCards = document.getElementById('initial-cards');

function loadVaccines(hospital_id=null){
    fetch(`?action=get_vaccines${hospital_id?'&hospital_id='+hospital_id:''}`)
    .then(res=>res.json())
    .then(data=>{
        initialCards.classList.add('hidden');
        dynamicCards.innerHTML='';
        data.forEach(v=>{
            let div = document.createElement('div');
            div.className='card vaccine';
            div.innerHTML=`<h3>${v.vaccine_name}</h3>
                           <p>${v.description.substring(0,50)}...</p>
                           <a class="btn-appointment" href="request_appointment.php?vaccine_id=${v.vaccine_id}">Get Appointment</a>`;
            div.onclick=()=>loadHospitals(v.vaccine_id);
            dynamicCards.appendChild(div);
        });
        dynamicCards.classList.remove('hidden');
    });
}

function loadHospitals(vaccine_id=null){
    fetch(`?action=get_hospitals${vaccine_id?'&vaccine_id='+vaccine_id:''}`)
    .then(res=>res.json())
    .then(data=>{
        initialCards.classList.add('hidden');
        dynamicCards.innerHTML='';
        data.forEach(h=>{
            let div = document.createElement('div');
            div.className='card hospital';
            div.innerHTML=`<h3>${h.hospital_name}</h3>
                           <p>${h.address}</p>
                           <a class="btn-appointment" href="request_appointment.php?hospital_id=${h.hospital_id}${vaccine_id?'&vaccine_id='+vaccine_id:''}">Get Appointment</a>`;
            div.onclick=()=>loadVaccines(h.hospital_id);
            dynamicCards.appendChild(div);
        });
        dynamicCards.classList.remove('hidden');
    });
}
</script>

</body>
</html>
