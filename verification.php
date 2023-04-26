<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_GET['download'])) {
  unset($_GET['download']);
  header("Location: download.php");
}

if(isset($_GET['parse'])) {
  unset($_GET['parse']);
  header("Location: parse.php");
}

if(isset($_GET['delete'])) {
  unset($_GET['delete']);
  $query1 = "DELETE FROM html;";
  $stmt1 = $db->query($query1); 
  header("Location: verification.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Overenie metód API</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.4/datatables.min.css"/>
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.4/datatables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
  <header>
		<nav>
			<ul>
        <li><a href="menu.php">Jedálny lístok</a></li>
				<li><a href="swagger.html">Popis API metód</a></li>
        <li><a href="verification.php">Overenie API metód</a></li>
			</ul>
		</nav>
	</header>
  <div class="container" class="header">
    <h1>Overenie API metód</h1>
    <div class="buttons">
      <a href="verification.php?download=true" class="button">Stiahni</a>
      <a href="verification.php?parse=true" class="button">Rozparsuj</a>
      <a href="verification.php?delete=true" class="button">Vymaž</a>
    </div>
    <h2>Upraviť cenu jedla</h2>
    <form action="" method="post" id="edit_meal">
      <div class="mb-3">
          <label for="meal" class="form-label">Vyber jedlo:</label>
          <select name="meal" id="meal" required>
          </select>
      </div>
      <div class="mb-3" id="edit-price">
          <label for="price" class="form-label">Uprav cenu:</label>
      </div>
      <button type="submit" name="submit" class="submit_button">Uložiť</button>
    </form>
    <h2>Pridať jedlo</h2>
    <form action="" method="post" id="add_meal">
      <div class="mb-3">
          <label for="name" class="form-label">Názov:</label>
          <input type="text" name="name" class="form-control" id="name" required>
      </div>
      <div class="mb-3">
          <label for="price" class="form-label">Cena:</label>
          <input type="text" name="price" class="form-control" id="price" required>
      </div>
      <div class="mb-3">
        <label for="place" class="form-label">Reštaurácia:</label>
        <input type="text" name="place" class="form-control" id="place" required>
      </div>
      <button type="submit" name="submit" class="submit_button">Uložiť</button>
    </form>
    <h2>Vymazať ponuku reštaurácie</h2>
    <form action="" method="post" id="delete_offer">
      <div class="mb-3">
          <label for="restaurant" class="form-label">Vyber reštauráciu:</label>
          <select name="restaurant" id="restaurant" required>
            <option value="Eat and Meet">Eat and Meet</option>
            <option value="Venza">Venza</option>
            <option value="FIIT Food">FIIT Food</option>
          </select>
      </div>
      <button type="submit" name="submit" class="submit_button">Vymaž</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"></script>
  <script>
    getData();
    addData();
    deleteData();
    
    async function getData() {
      axios.get('https://site29.webte.fei.stuba.sk/restaurants/api')
      .then(function (response) {
        // Extract the data from the API response
        const data = response.data;
        var select = document.getElementById('meal');
        var price_input = document.getElementById('edit-price');

        for (var i = 0; i < data.length; i++) {
          var option = document.createElement("option");
          option.value = data[i].id;
          option.text = `${data[i].place}: ${data[i].name}`;
          select.appendChild(option);
        }

        // Set up event listener for the select element
        select.addEventListener("change", function () {
          var selected_id = select.options[select.selectedIndex].value;

          axios
            .get(
              `https://site29.webte.fei.stuba.sk/restaurants/api?id=${selected_id}`
            )
            .then((res) => {
              // Update the placeholder of the input element
              price_input.querySelector("input").placeholder =
                res.data[0].price;
            })
            .catch(function (error) {
              console.log(error.response);
            });
        });

        // Initialize the placeholder of the input element
        var selected_id = select.options[select.selectedIndex].value;
        axios
          .get(`https://site29.webte.fei.stuba.sk/restaurants/api?id=${selected_id}`)
          .then((res) => {
            var input = document.createElement("input");
            input.type = "text";
            input.name = "price";
            input.class = "form-control";
            input.required;
            input.placeholder = res.data[0].price;

            price_input.appendChild(input);

            var form = document.getElementById("edit_meal");

            form.addEventListener("submit", function (event) {
              event.preventDefault(); // Prevent the default form submission

              new_price = input.value;
              // Send the updated price to the server using axios
              axios
                .put(
                  `https://site29.webte.fei.stuba.sk/restaurants/api?id=${selected_id}`,
                  { price: new_price }
                )
                .then((res) => {
                  alert(`Cena úspešne upravená.`)
                })
                .catch(function (error) {
                  console.log(error.response);
                });
            });
          })
          .catch(function (error) {
            console.log(error.response);
          });
      })
      .catch(function (error) {
        console.log(error.response.data);
      });
    }

    function addData() {
      // Get the form element
      var form = document.getElementById("add_meal");

      // Add an event listener to the form's submit event
      form.addEventListener('submit', (event) => {
        // Prevent the default form submission behavior
        event.preventDefault();
        
        // Create a new FormData object
        const formData = new FormData(form);
        let data = Object.fromEntries(formData.entries());

        // Send the data as a POST request using Axios
        for(let i = 1; i <= 7; i++) {
          data.day = `${i}`;
          if(i===7) {
            alert(`Jedlo úspešne pridané do ponuky.`);
          }
          axios.post('https://site29.webte.fei.stuba.sk/restaurants/api', JSON.stringify(data), {
          headers: {
            'Content-Type': 'application/json'
          }
        })
          .catch((error) => {
            console.log(error);
          });
        }
      });
    }

    function deleteData() {
      // Get the form element
      var form = document.getElementById("delete_offer");
      var select = document.getElementById('restaurant');

      // Add an event listener to the form's submit event
      form.addEventListener('submit', (event) => {
        // Prevent the default form submission behavior
        event.preventDefault();
        
        // Create a new FormData object
        var selected_name = select.options[select.selectedIndex].value;

        axios.delete(`https://site29.webte.fei.stuba.sk/restaurants/api?name=${selected_name}`)
        .then((res) => {
          alert(`Ponuka reštaurácie ${selected_name} úspešne vymazaná.`)
        })
        .catch((error) => {
          console.log(error);
        });
      });
    }
  </script>
</body>
</html>

