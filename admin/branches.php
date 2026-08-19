<?php
require "auth.php";
require "../config/database.php";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$notice = $_GET["notice"] ?? "";
$error = "";
$editing = [
    "id" => "",
    "name" => "",
    "address" => "",
    "map_location" => "",
    "phone" => "",
    "email" => "",
    "status" => 1,
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"] ?? "")) {
        $error = "Your session has expired. Please try again.";
    } else {
        $action = $_POST["action"] ?? "";
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT) ?: 0;

        if ($action === "save") {
            $name = trim($_POST["name"] ?? "");
            $address = trim($_POST["address"] ?? "");
            $map_location = trim($_POST["map_location"] ?? "");
            $phone = trim($_POST["phone"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $status = isset($_POST["status"]) ? 1 : 0;
            $editing = compact("id", "name", "address", "map_location", "phone", "email", "status");

            if ($name === "" || $address === "" || $map_location === "") {
                $error = "Branch name, address, and map location are required.";
            } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
            } else {
                $duplicate = $pdo->prepare("SELECT id FROM branches WHERE name = ? AND id != ? LIMIT 1");
                $duplicate->execute([$name, $id]);
                if ($duplicate->fetch()) {
                    $error = "A branch with this name already exists.";
                } elseif ($id) {
                    $pdo->prepare("UPDATE branches SET name = ?, address = ?, map_location = ?, phone = ?, email = ?, status = ? WHERE id = ?")->execute([$name, $address, $map_location, $phone ?: null, $email ?: null, $status, $id]);
                    header("Location: branches.php?notice=Branch updated.");
                    exit();
                } else {
                    $pdo->prepare("INSERT INTO branches (name, address, map_location, phone, email, status) VALUES (?, ?, ?, ?, ?, ?)")->execute([$name, $address, $map_location, $phone ?: null, $email ?: null, $status]);
                    header("Location: branches.php?notice=Branch added.");
                    exit();
                }
            }
        } elseif ($action === "delete" && $id) {
            $pdo->prepare("DELETE FROM branches WHERE id = ?")->execute([$id]);
            header("Location: branches.php?notice=Branch deleted.");
            exit();
        }
    }
}

if (!$error && isset($_GET["edit"]) && ($id = filter_input(INPUT_GET, "edit", FILTER_VALIDATE_INT))) {
    $statement = $pdo->prepare("SELECT id, name, address, map_location, phone, email, status FROM branches WHERE id = ?");
    $statement->execute([$id]);
    $editing = $statement->fetch() ?: $editing;
}

$branches = $pdo->query("SELECT * FROM branches ORDER BY name ASC")->fetchAll();
include "includes/header.php";
include "includes/sidebar.php";
include "includes/navbar.php";
?>
<div class="content"><div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-4"><h2>Branches</h2><a href="branches.php" class="btn btn-outline-secondary">New Branch</a></div>
<?php if ($notice): ?><div class="alert alert-success"><?= htmlspecialchars($notice, ENT_QUOTES, "UTF-8") ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></div><?php endif; ?>
<div class="row g-4"><div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><h5><?= $editing["id"] ? "Edit Branch" : "Add Branch" ?></h5><form method="post"><input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $editing["id"] ?>"><div class="mb-3"><label class="form-label" for="branchName">Branch name</label><input class="form-control" id="branchName" name="name" maxlength="150" value="<?= htmlspecialchars($editing["name"], ENT_QUOTES, "UTF-8") ?>" required></div><div class="mb-3"><label class="form-label" for="branchAddress">Address</label><input class="form-control" id="branchAddress" name="address" maxlength="255" value="<?= htmlspecialchars($editing["address"], ENT_QUOTES, "UTF-8") ?>" placeholder="Add address" required></div><div class="mb-3"><label class="form-label" for="mapLocation">Map location</label><input class="form-control" id="mapLocation" name="map_location" maxlength="255" value="<?= htmlspecialchars($editing["map_location"], ENT_QUOTES, "UTF-8") ?>" placeholder="Select location" required><div class="form-text">Enter the place or full address to show on Google Maps.</div></div><div class="mb-3"><label class="form-label">Map preview</label><div class="ratio ratio-4x3"><iframe id="branchMapPreview" src="https://www.google.com/maps?q=<?= rawurlencode($editing["map_location"] ?: "Itahari, Sunsari, Nepal") ?>&amp;output=embed" title="Branch map preview" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div><a id="branchMapLink" class="btn btn-outline-dark btn-sm mt-2" href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode($editing["map_location"] ?: "Itahari, Sunsari, Nepal") ?>" target="_blank" rel="noopener">Open in Google Maps</a></div><div class="mb-3"><label class="form-label" for="branchPhone">Phone</label><input class="form-control" id="branchPhone" name="phone" maxlength="30" value="<?= htmlspecialchars($editing["phone"] ?? "", ENT_QUOTES, "UTF-8") ?>"></div><div class="mb-3"><label class="form-label" for="branchEmail">Email</label><input class="form-control" id="branchEmail" type="email" name="email" maxlength="150" value="<?= htmlspecialchars($editing["email"] ?? "", ENT_QUOTES, "UTF-8") ?>"></div><div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="status" id="branchStatus" <?= $editing["status"] ? "checked" : "" ?>><label class="form-check-label" for="branchStatus">Show on website</label></div><button class="btn btn-warning">Save Branch</button></form></div></div></div><div class="col-lg-8"><div class="card shadow-sm"><div class="card-body table-responsive"><table class="table align-middle"><thead class="table-dark"><tr><th>Branch</th><th>Contact</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php if (!$branches): ?><tr><td colspan="4" class="text-center text-muted">No branches added yet.</td></tr><?php endif; ?><?php foreach ($branches as $branch): ?><tr><td><strong><?= htmlspecialchars($branch["name"], ENT_QUOTES, "UTF-8") ?></strong><div class="small text-muted"><?= htmlspecialchars($branch["address"], ENT_QUOTES, "UTF-8") ?></div></td><td class="small"><?php if ($branch["phone"]): ?><div><?= htmlspecialchars($branch["phone"], ENT_QUOTES, "UTF-8") ?></div><?php endif; ?><?php if ($branch["email"]): ?><div><?= htmlspecialchars($branch["email"], ENT_QUOTES, "UTF-8") ?></div><?php endif; ?></td><td><span class="badge bg-<?= $branch["status"] ? "success" : "secondary" ?>"><?= $branch["status"] ? "Visible" : "Hidden" ?></span></td><td><a class="btn btn-sm btn-outline-dark" href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode($branch["map_location"]) ?>" target="_blank" rel="noopener" aria-label="View <?= htmlspecialchars($branch["name"], ENT_QUOTES, "UTF-8") ?> on Google Maps"><i class="fas fa-map-marked-alt"></i></a><a class="btn btn-sm btn-primary" href="branches.php?edit=<?= $branch["id"] ?>" aria-label="Edit <?= htmlspecialchars($branch["name"], ENT_QUOTES, "UTF-8") ?>"><i class="fas fa-edit"></i></a><form class="d-inline" method="post" onsubmit="return confirm('Delete this branch?');"><input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $branch["id"] ?>"><button class="btn btn-sm btn-danger" aria-label="Delete <?= htmlspecialchars($branch["name"], ENT_QUOTES, "UTF-8") ?>"><i class="fas fa-trash"></i></button></form></td></tr><?php endforeach; ?></tbody></table></div></div></div></div></div></div>
<script>
const mapLocation = document.getElementById("mapLocation");
const mapPreview = document.getElementById("branchMapPreview");
const mapLink = document.getElementById("branchMapLink");
const branchAddress = document.getElementById("branchAddress");
mapPreview.closest(".mb-3").remove();

const leafletStyle = document.createElement("link");
leafletStyle.rel = "stylesheet";
leafletStyle.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
document.head.appendChild(leafletStyle);

const leafletScript = document.createElement("script");
leafletScript.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
leafletScript.onload = () => {
    const pickerLabel = document.createElement("label");
    pickerLabel.className = "form-label mt-3";
    pickerLabel.textContent = "Select location on map";
    const picker = document.createElement("div");
    picker.id = "branchLocationPicker";
    picker.style.height = "300px";
    picker.style.borderRadius = "0.375rem";
    picker.style.overflow = "hidden";
    mapLocation.closest(".mb-3").after(pickerLabel, picker);

    const map = L.map(picker).setView([26.663, 87.274], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);
    let marker;

    map.on("click", async (event) => {
        const { lat, lng } = event.latlng;
        const coordinates = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        if (marker) {
            marker.setLatLng(event.latlng);
        } else {
            marker = L.marker(event.latlng).addTo(map);
        }
        mapLocation.value = coordinates;
        branchAddress.value = "Finding address...";

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`,
            );
            const location = await response.json();
            branchAddress.value = location.display_name || coordinates;
        } catch (error) {
            branchAddress.value = coordinates;
        }
    });
};
document.head.appendChild(leafletScript);
</script>
<?php include "includes/footer.php"; ?>
