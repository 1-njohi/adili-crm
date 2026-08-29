<!DOCTYPE html>
<html>
<head>
    <title>Your Lead Has Purchased a Plot</title>
</head>
<body>
    <h1>Congratulations {{ $agentName }}!</h1>
    <p>A lead you brought has purchased a plot!</p>
    <ul>
        <li><strong>Plot:</strong> #{{ $plotNumber }}</li>
        <li><strong>Project:</strong> {{ $projectName }}</li>
        <li><strong>Buyer:</strong> {{ $buyerName }} ({{ $buyerEmail }})</li>
    </ul>
    <p>We will update you on the commission status shortly.</p>
    <p>Thank you,<br>Adili Real Estate Team</p>
</body>
</html>
