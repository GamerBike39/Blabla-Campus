<!doctype html>
<html lang="fr">

<head>
    <title>Réinitialiser mon mot de passe</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="col-11">
            <div class="card text-center m-4 shadow p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <h4 class="card-title p-3">Réinitialiser mon mot de passe</h4>
                    <div class="form-group">
                        <form action="PHP/includes/pass2.inc.php" method="POST">
                            <input type="hidden" name="token"
                                value="<?php echo base64_decode(htmlspecialchars($_GET['u'])); ?>" />
                            <input type="password" name="password" class="form-control" placeholder="Mot de passe"
                                required />
                            <br />
                            <input type="password" name="password_repeat" class="form-control"
                                placeholder="Re-tapez le mot de passe" required />
                            <button type="submit" class="btn btn-primary btn-lg m-3">Modifier</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>