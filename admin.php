<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: sans-serif;
      }
      .container {
        max-width: 1200px;
        margin-inline: auto;
        padding-block: 60px;
      }
      .form-group {
        margin-bottom: 20px;
      }
      label {
        font-size: 15px;
        display: block;
        margin-bottom: 8px;
      }
      input {
        height: 45px;
        padding: 10px 12px;
        border: 1px solid #00000040;
        outline-color: gray;
        width: 100%;
      }
      #updateForm {
        max-width: 500px;
      }
      button[type='submit'] {
        padding: 12px 25px;
        cursor: pointer;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <form id="updateForm" enctype="multipart/form-data">
        <img id="preview" src="./assets/imgs/products/digihyip.png" alt="preview" style="width: 350px; margin-bottom: 40px" />
        <div class="form-group">
          <label>Title:</label>
          <input type="text" name="title" id="title" value="Digihyip - Hyip Investment Platform Html Template" />
        </div>
        <div class="form-group">
          <label>Category:</label>
          <input type="text" name="category" id="category" value="Hyip" />
        </div>
        <div class="form-group">
          <label>Image:</label>
          <input type="file" name="image" id="image" accept="image/*" />
        </div>
        <div class="form-group">
          <label>Link:</label>
          <input type="text" name="link" id="link" value="/template/" />
        </div>
        <button type="submit">Update Item</button>
      </form>
    </div>

    <script>
      document.getElementById("image").addEventListener("change", function (event) {
        const preview = document.getElementById("preview");
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            preview.src = e.target.result;
          };
          reader.readAsDataURL(file);
        }
      });

      document.getElementById("updateForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch("1_upload.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              alert("Item updated successfully!");
            } else {
              alert("Error updating item.");
            }
          });
      });
    </script>
  </body>
</html>
