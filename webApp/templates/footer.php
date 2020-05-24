    <footer class="section">
      <!-- Automatically updates the year with PHP -->
      <div class="center grey-text">Copyright <?php echo date("Y") ?> Centennial FBLA</div>
    </footer>

    <!-- Including the Javascript website responsiveness scripts -->
    <?php if($_SERVER['PHP_SELF'] === '/FBLA_Coding-Programming_2020_WebApp-optimize_041220/webApp/index.php'): ?>
      <script type="text/javascript">
        <?php require 'includes/cardAdapt.inc.js'; ?>
      </script>
      <script type="text/javascript">
        <?php require 'includes/navAdapt.inc.js'; ?>
      </script>
    <?php else: ?>
      <script type="text/javascript">
        <?php require 'includes/navAdapt.inc.js'; ?>
      </script>
    <?php endif; ?>
  </body>
</html>
