<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="wrapper">
        <div class="form-wrapper">
            <h1>Service Request</h1>
            <form method="POST" action="action.php">
                <input type="text" name="name" placeholder="Full Name" required>
                 <input type="text" name="Phone" placeholder="Phone" required>
                <input type="text" name="Pick-up" placeholder="Pick-up:" required>
                 <input type="text" name="Drop-off" placeholder="Drop-off:" required>
        
                <input type="text" name="Price" placeholder="Price" required>
                <textarea name="Description" placeholder="Description" required></textarea>
                 <select name="role" required>
                    <option value="">--Select Service--</option>
                    <option value="user">Food Delivery</option>
                    <option value="rider">Pabili</option>
                    <option value="admin">Angkas</option>
                      <option value="admin">Padala</option>
                      

                </select>
              <div class="btn-box">
    <button type="submit">Submit</button>
   <button type="button" onclick="window.location.href='user_page.php'">Cancel</button>
</div>
            </form>
        </div>
    </div>

</body>
</html>