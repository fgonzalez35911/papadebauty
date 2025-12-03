<?php include 'includes/header.php'; ?>
<style>.polaroid {background:white; padding:10px 10px 40px 10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); text-align:center; transform:rotate(-2deg); border:1px solid #eee;} .polaroid:nth-child(even){transform:rotate(2deg);} </style>
<div style="padding:40px 20px;">
    <h1 style="text-align:center; color:var(--color-primario);">Galería</h1>
    <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:30px; margin-top:40px;">
        <?php $res = $conn->query("SELECT * FROM galeria ORDER BY id DESC"); while($r = $res->fetch_assoc()): ?>
            <div class="polaroid" style="width:280px;">
                <img src="<?php echo $r['imagen']; ?>" style="width:100%; height:250px; object-fit:cover; filter:grayscale(20%);">
                <div style="margin-top:15px; font-weight:bold; font-family:'Courier New'; color:#555;"><?php echo $r['titulo']; ?></div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>