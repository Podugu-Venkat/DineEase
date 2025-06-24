<?php include_once('../components/header.php')?>
<section id="hero" style="position: relative;">
<img src="../image/homepage.png" alt="img" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
    <div class="hero container" style="position: relative; z-index: 1;">
        <div>
            <h1><strong><h1 class="text-center" style="font-family:Copperplate; color:whitesmoke;"> DineEase</h1><span></span></strong></h1>
            <a href="#projects" type="button" class="cta">MENU</a>
        </div>
    </div>
</section>
  
  
  
  <!-- menu Section -->
  <section id="projects">
    <div class="projects container">
      <div class="projects-header">
        <h1 class="section-title">Me<span>n</span>u</h1>
      </div>
     
        
       <select style="text-align:center;" id="menu-category" class="menu-category">
        <option value="blue">ALL ITEMS</option>
        <option value="yellow">MAIN DISHES</option>
        <option value="red">SIDE DISHES</option>
        <option value="green">DRINKS</option>
      </select>
        
    <!-- Updated CSS to collapse nutrient content completely when not hovered -->
    <style>
        .item-name {
            position: relative;
            display: inline-block;
            text-align: left;
        }
        .item-nutrients {
            position: absolute; /* Remove it from the document flow */
            left: 0;
            top: 100%; /* Position it directly below the item name */
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: opacity 0.3s ease, max-height 0.3s ease;
            color: #333;
            font-size: 0.9em;
            background: rgba(255, 255, 255, 0.9);
            padding: 5px;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .item-name:hover .item-nutrients {
            opacity: 1;
            max-height: 200px; /* Adjust to fit the content */
        }
        .item-name::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: black;
            transition: width 0.3s ease;
            margin-top: 5px;
        }
        .item-name:hover::after {
            width: 100%;
        }
    </style>

    <div class="yellow msg"> 
     
        <div></div>
      <div class="mainDish">
           <h1 style="text-align:center;">MAIN DISHES</h1>
          <?php foreach ($mainDishes as $item): ?>
      <p>
        <span class="item-name"> 
          <strong><?php echo $item['item_name']; ?></strong>
          <?php if (isset($item['avg_rating'])): ?>
          <span class="item-rating"><?php echo $item['avg_rating']; ?> ⭐</span>
          <?php endif; ?>
          <span class="item-price">Rs.<?php echo $item['item_price']; ?></span>
          <?php if (isset($item['calories'])): ?>
          <span class="item-nutrients">
            Nutrients: Calories: <?php echo $item['calories']; ?> kcal | Protein: <?php echo $item['protein_g']; ?>g | Fat: <?php echo $item['fat_g']; ?>g | Carbs: <?php echo $item['carbs_g']; ?>g
          </span>
          <?php endif; ?>
        </span>
        <span class="item_type"><i><?php echo $item['item_type']; ?></i></span>
        <hr>
        
      </p>
    <?php endforeach; ?>
      </div>
    </div>
      
      
    <div class="red msg">
        <div></div>
      <div class="sideDish">
           <h1 style="text-align:center">SIDE DISHES</h1>
          <?php foreach ($sides as $item): ?>
      <p>
        <span class="item-name"> 
          <strong><?php echo $item['item_name']; ?></strong>
          <?php if (isset($item['avg_rating'])): ?>
          <span class="item-rating"><?php echo $item['avg_rating']; ?> ⭐</span>
          <?php endif; ?>
          <span class="item-price">Rs.<?php echo $item['item_price']; ?></span>
          <?php if (isset($item['calories'])): ?>
          <span class="item-nutrients">
            Nutrients: Calories: <?php echo $item['calories']; ?> kcal | Protein: <?php echo $item['protein_g']; ?>g | Fat: <?php echo $item['fat_g']; ?>g | Carbs: <?php echo $item['carbs_g']; ?>g
          </span>
          <?php endif; ?>
        </span>
        <span class="item_type"><i><?php echo $item['item_type']; ?></i></span>
        <hr>
      </p>
    <?php endforeach; ?>
      </div>
    </div>
        
      
      
    <div class="green msg">
        <div></div>
      <div class="drinks">
           <h1 style="text-align:center">DRINKS</h1>
          <?php foreach ($drinks as $item): ?>
      <p>
        <span class="item-name"> 
          <strong><?php echo $item['item_name']; ?></strong>
          <?php if (isset($item['avg_rating'])): ?>
          <span class="item-rating"><?php echo $item['avg_rating']; ?> ⭐</span>
          <?php endif; ?>
          <span class="item-price">Rs.<?php echo $item['item_price']; ?></span>
          <?php if (isset($item['calories'])): ?>
          <span class="item-nutrients">
            Nutrients: Calories: <?php echo $item['calories']; ?> kcal | Protein: <?php echo $item['protein_g']; ?>g | Fat: <?php echo $item['fat_g']; ?>g | Carbs: <?php echo $item['carbs_g']; ?>g
          </span>
          <?php endif; ?>
        </span>
        <span class="item_type"><i><?php echo $item['item_type']; ?></i></span>
        <hr>
      </p>
    <?php endforeach; ?>
      </div>
    </div>
      
      
       <div class="blue msg">
          
      <div class="mainDish">
           <h1 style="text-align:center">MAIN DISHES</h1>
          <?php foreach ($mainDishes as $item): ?>
      <p>
        <span class="item-name"> 
          <strong><?php echo $item['item_name']; ?></strong>
          <?php if (isset($item['avg_rating'])): ?>
          <span class="item-rating"><?php echo $item['avg_rating']; ?> ⭐</span>
          <?php endif; ?>
          <span class="item-price">Rs.<?php echo $item['item_price']; ?></span>
          <?php if (isset($item['calories'])): ?>
          <span class="item-nutrients">
            Nutrients: Calories: <?php echo $item['calories']; ?> kcal | Protein: <?php echo $item['protein_g']; ?>g | Fat: <?php echo $item['fat_g']; ?>g | Carbs: <?php echo $item['carbs_g']; ?>g
          </span>
          <?php endif; ?>
        </span>
        <span class="item_type"><i><?php echo $item['item_type']; ?></i></span>
        <hr>
      </p>
    <?php endforeach; ?>
      </div>
             
           
     
      <div class="sideDish">
           <h1 style="text-align:center">SIDE DISHES</h1>
          <?php foreach ($sides as $item): ?>
      <p>
        <span class="item-name"> 
          <strong><?php echo $item['item_name']; ?></strong>
          <?php if (isset($item['avg_rating'])): ?>
          <span class="item-rating"><?php echo $item['avg_rating']; ?> ⭐</span>
          <?php endif; ?>
          <span class="item-price">Rs.<?php echo $item['item_price']; ?></span>
          <?php if (isset($item['calories'])): ?>
          <span class="item-nutrients">
            Nutrients: Calories: <?php echo $item['calories']; ?> kcal | Protein: <?php echo $item['protein_g']; ?>g | Fat: <?php echo $item['fat_g']; ?>g | Carbs: <?php echo $item['carbs_g']; ?>g
          </span>
          <?php endif; ?>
        </span>
        <span class="item_type"><i><?php echo $item['item_type']; ?></i></span>
        <hr>
      </p>
    <?php endforeach; ?>
      </div>
            
      
      <div class="drinks">
           <h1 style="text-align:center">DRINKS</h1>
          <?php foreach ($drinks as $item): ?>
      <p>
        <span class="item-name"> 
          <strong><?php echo $item['item_name']; ?></strong>
          <?php if (isset($item['avg_rating'])): ?>
          <span class="item-rating"><?php echo $item['avg_rating']; ?> ⭐</span>
          <?php endif; ?>
          <span class="item-price">Rs.<?php echo $item['item_price']; ?></span>
          <?php if (isset($item['calories'])): ?>
          <span class="item-nutrients">
            Nutrients: Calories: <?php echo $item['calories']; ?> kcal | Protein: <?php echo $item['protein_g']; ?>g | Fat: <?php echo $item['fat_g']; ?>g | Carbs: <?php echo $item['carbs_g']; ?>g
          </span>
          <?php endif; ?>
        </span>
        <span class="item_type"><i><?php echo $item['item_type']; ?></i></span>
        <hr>
      </p>
    <?php endforeach; ?>
      </div>
          
      </div>
    </div>
  </section>
  <!-- End menu Section -->

<!-- Updated JS to add/remove the expanded class -->
<script>
document.querySelectorAll('.item-name').forEach(function(el) {
    el.addEventListener('mouseenter', function() {
        var nutrients = el.querySelector('.item-nutrients');
        if (nutrients) {
            nutrients.classList.add('expanded');
        }
    });
    el.addEventListener('mouseleave', function() {
        var nutrients = el.querySelector('.item-nutrients');
        if (nutrients) {
            nutrients.classList.remove('expanded');
        }
    });
});
</script>

  
  <!-- About Section -->
<section id="about" ">
  <div class="about container">
    <div class="col-right">
        <h1 class="section-title" >About <span>Us</span></h1>
        <h2>DineEase Company History:</h2>
 <p>DineEase is a well-established Western food establishment in the city's heart. DineEase has become a popular choice for customers looking to celebrate special occasions or simply enjoy a relaxing meal, with a focus on providing delicious meals and a friendly dining experience.
 </p>
 <p>DineEase, as a Western restaurant, offers a diverse menu that caters to a variety of tastes. The menu includes a wide range of options such as bar bites, salads, soups and a variety of main courses. Customers can savour succulent options such as steak and ribs, chicken, lamb, seafood, burgers and sandwiches, pasta, and a variety of delectable side dishes. The menu has been carefully curated to offer a balance of classic favourites and innovative creations, ensuring that every palate is satisfied.
 </p>
 <p>DineEase's ability to accommodate customers is one of its distinguishing features. DineEase strives to create an inviting and comfortable dining environment, whether guests prefer to walk in or make reservations in advance. The restaurant recognises the significance of creating memorable experiences, particularly for those celebrating special occasions. DineEase is a popular choice for families, couples, and groups of friends because of its attentive staff and welcoming atmosphere.
 </p>
 <p>DIneEase has an inviting outdoor bar that is open seven days a week from 11:00 AM to 10:00 PM in addition to the indoor dining area.This outdoor space provides a relaxed setting for patrons to unwind and socialise while sipping on their favourite drinks and nibbling on bar bites. The bar serves a wide range of beverages, including cocktails, wines, beers and non-alcoholic options.
 </p>
    
      </div>
    </div>
  </section>
  <!-- End About Section -->
  
  
 <!-- Contact Section -->
<section id="contact">
  <div class="contact container">
    <div>
      <h1 class="section-title">Contact <span>info</span></h1>
    </div>
    <div class="contact-items">
      <div class="contact-item contact-item-bg">
        <div class="contact-info">
          <div class='icon'><img src="../image/icons8-phone-100.png" alt=""/></div>
          <h1>Phone</h1>
          <h2>+91 987651234</h2>
        </div>
      </div>
      
      <div class="contact-item contact-item-bg"> 
        <div class="contact-info">
          <div class='icon'><img src="../image/icons8-email-100.png" alt=""/></div>
          <h1>Email</h1>
          <h2>DineEase@gmail.com</h2> 
        </div>
      </div>
      
      <div class="contact-item contact-item-bg">
        <div class="contact-info">
          <div class='icon'> <img src="../image/icons8-home-address-100.png" alt=""/></div>
          <h1>Address</h1>
          <h2>New Delhi</h2>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End Contact Section -->

<?php 
include_once('../components/footer.php');
?>