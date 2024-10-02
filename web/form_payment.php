<?php
    use lab\Operator\AfribaPAY;
    require(dirname(__DIR__, 1).'/vendor/autoload.php');
    $oper = new AfribaPAY('sandbox');
    $paysData = $oper->listPays();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfribaPAY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }
        button {
            background-color: #007bff;
            border-color: #007bff;
        }
        button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="text-center mb-4">
            <img src="afribapay.png" alt="Logo" class="img-fluid" style="max-width: 200px;">
        </div>
        <h1>Formulaire de Paiement</h1>
        <form action="process_payment.php" method="POST">
            <div class="mb-3">
                <label for="country" class="form-label">Choisissez le pays :</label>
                <select id="country" name="country" class="form-select" required>
                    <option value="">Sélectionner un pays</option>
                    <?php foreach ($paysData['content']['data'] as $countryCode => $country) : ?>
                        <option value="<?= htmlspecialchars($countryCode) ?>"><?= htmlspecialchars($country['country_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="currency" class="form-label">Choisissez la devise :</label>
                <select id="currency" name="currency" class="form-select" required>
                    <option value="">Sélectionner une devise</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="operator" class="form-label">Choisissez l'opérateur :</label>
                <select id="operator" name="operator" class="form-select" required>
                    <option value="">Sélectionner un opérateur</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Numéro de téléphone :</label>
                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{9,15}" required>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Montant :</label>
                <input type="number" id="amount" name="amount" class="form-control" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Valider</button>
        </form>
    </div>

    <script>
        const data = <?php echo json_encode($paysData['content']['data'], JSON_HEX_TAG); ?>;

        const countrySelect = document.getElementById('country');
        const currencySelect = document.getElementById('currency');
        const operatorSelect = document.getElementById('operator');

        countrySelect.addEventListener('change', function () {
            const selectedCountry = this.value;
            currencySelect.innerHTML = '<option value="">Sélectionner une devise</option>';
            operatorSelect.innerHTML = '<option value="">Sélectionner un opérateur</option>';

            if (selectedCountry && data[selectedCountry]) {
                const currencies = data[selectedCountry].currencies;
                for (let currency in currencies) {
                    const option = document.createElement('option');
                    option.value = currency;
                    option.textContent = currency;
                    currencySelect.appendChild(option);
                }
            }
        });

        currencySelect.addEventListener('change', function () {
            const selectedCountry = countrySelect.value;
            const selectedCurrency = this.value;
            operatorSelect.innerHTML = '<option value="">Sélectionner un opérateur</option>';

            if (selectedCountry && selectedCurrency && data[selectedCountry].currencies[selectedCurrency]) {
                const operators = data[selectedCountry].currencies[selectedCurrency].operators;
                operators.forEach(operator => {
                    const option = document.createElement('option');
                    option.value = operator.operator_code;
                    option.textContent = operator.operator_name;
                    operatorSelect.appendChild(option);
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
