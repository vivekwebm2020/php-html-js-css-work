<?php
session_start();

function getCurrentUrlWithoutParams() {
    // Get the protocol (http or https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";

    // Get the host
    $host = $_SERVER['HTTP_HOST'];

    // Get the request URI
    $requestUri = $_SERVER['REQUEST_URI'];

    // Parse the URL and get the path
    $urlPath = strtok($requestUri, '?'); // This removes the query string

    // Construct the full URL without parameters
    return $protocol . $host . $urlPath;
}

// Usage
$currentUrl = getCurrentUrlWithoutParams();

function getCurrentPath() {
    return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
}

if (!function_exists('getSubFolderUri')) {
    function getSubFolderUri() {
        // Get the full URI
        $requestUri = $_SERVER['REQUEST_URI'];

        // Get the host from the server variables
        $host = $_SERVER['HTTP_HOST'];

        // Get the protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

        // Construct the full URL (not necessary for your requirement but useful)
        $fullUrl = $protocol . $host . $requestUri;

        // Find the position of the question mark to separate the URL parameters
        $questionMarkPos = strpos($requestUri, '?');

        // Extract the path part without the parameters
        if ($questionMarkPos !== false) {
            $pathPart = substr($requestUri, 0, $questionMarkPos);
        } else {
            $pathPart = $requestUri;
        }

        // Trim leading slashes for a cleaner output
        $pathPart = ltrim($pathPart, '/');

        // Output the result
        return htmlspecialchars($pathPart);
    }
}

if (!function_exists('destroySession')) {
    function destroySession() {
        // Unset all session variables
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session
        session_destroy();
    }
}

// Function to destroy session
if (isset($_GET['action']) && $_GET['action'] === 'destroy_session') {
    destroySession();
    header("Location: " . $currentUrl);
    exit();
}

// Initialize betting balance if not already set
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 200;
    $_SESSION['betting_fee'] = 10;
}

$sum = 0;
$result = "";

if (!function_exists('updateBalance')) {
    function updateBalance($type = 'win', $betAmount) {
        $_SESSION['balance'] -= $_SESSION['betting_fee'];
        if ($type == 'win') {
            $_SESSION['balance'] += $betAmount;
        } else {
            $_SESSION['balance'] -= $betAmount;
        }
        return $_SESSION;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the bet amount and user's choice
    $betAmount = intval($_POST['betAmount']);
    $userBet = $_POST['bet'];

    // Check if the bet amount is valid
    if ($betAmount > 0 && ($betAmount + $_SESSION['betting_fee']) <= $_SESSION['balance']) {
        // Generate two random dice rolls
        $dice1 = random_int(1, 6);
        $dice2 = random_int(1, 6);
        $sum = $dice1 + $dice2;

        // Determine the result and update the balance
        if ($sum < 7 && $userBet == 'below') {
            $result = "You win! The sum is $sum (below 7).";
            updateBalance('win', $betAmount);
        } elseif ($sum == 7 && $userBet == 'lucky') {
            $result = "You win! The sum is $sum (exactly 7).";
            updateBalance('win', $betAmount);
        } elseif ($sum > 7 && $userBet == 'above') {
            $result = "You win! The sum is $sum (above 7).";
            updateBalance('win', $betAmount);
        } else {
            $result = "You lose! The sum is $sum.";
            updateBalance('lose', $betAmount);
        }
    } else {
        $result = "Invalid bet amount. Please enter a valid amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dice Betting Game</title>
</head>
<body>
    <h1>Dice Betting Game</h1>
    <p>Your current balance: $<?php echo $_SESSION['balance']; ?></p>

    <form method="POST">
        <label for="betAmount">Bet Amount:</label>
        <input type="number" id="betAmount" name="betAmount" min="1" max="<?php echo $_SESSION['balance']; ?>" required>
        <br><br>

        <label>Place your bet:</label><br>
        <input type="radio" id="below" name="bet" value="below" required>
        <label for="below">Below 7</label><br>
        <input type="radio" id="lucky" name="bet" value="lucky" required>
        <label for="lucky">Lucky 7</label><br>
        <input type="radio" id="above" name="bet" value="above" required>
        <label for="above">Above 7</label><br><br>

        <input type="submit" value="Roll Dice">
    </form>

    <?php if ($result): ?>
        <h2>Result:</h2>
        <p><?php echo $result; ?></p>
    <?php endif; ?>

    <form method="POST" action="?action=destroy_session">
        <input type="submit" value="Reset Game">
    </form>

    <?php echo "<br><b>Note:</b>&nbsp;<span>$10 Betting Fee will be charged on each bet.</<span>"; ?>
</body>
</html>
