<?php include 'includes/header.php'; ?>
<div style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="color: var(--color-primario); text-align: center;">Video Tutoriales</h1>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top:40px;">
        <?php $res = $conn->query("SELECT * FROM tutoriales ORDER BY id DESC"); while($r = $res->fetch_assoc()): ?>
            <div>
                <iframe width="100%" height="200" src="https://www.youtube.com/embed/<?php echo $r['video_url']; ?>" frameborder="0" allowfullscreen style="border-radius: 15px;"></iframe>
                <h3 style="margin-top: 10px; font-size:1.2rem; color:#444;"><?php echo $r['titulo']; ?></h3>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>