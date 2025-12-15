<?php require __DIR__ . '/app/autoload.php'; ?>
<?php require __DIR__ . '/views/header.php'; ?>


<?php
$statement = $database->query("SELECT * FROM rooms");

$rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
var_dump($rooms);

?>

<?php require __DIR__ . "/views/footer.php"; ?>