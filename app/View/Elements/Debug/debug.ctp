<div class="debug-area">
  <h2>Debug Area</h2>
  <ul>
    <li>
      <h3>Arrays</h3>
      <?php if (isset($arrays)): ?>
        <ul>
          <?php foreach($arrays as $name => $array): ?>
            <li>
              <h4><?= h($name); ?></h4>
              <pre><?php print_r($array); ?></pre>
            </li>
          <?php endforeach; ?>
          <li>
            <h4>$this->Session->read()</h4>
            <pre><?php print_r($this->Session->read()); ?></pre>
          </li>
        </ul>
      <?php else: ?>
        <?= 'No Entries'; ?>
      <?php endif; ?>
    </li>

    <li>
      <h3>Primitives</h3>
      <?php if (isset($primitives)): ?>
        <ul>
          <li>
            <?php foreach($primitives as $name => $value): ?>
              <h4><?= h($name); ?></h4>
              <pre><?= $value; ?></pre>
            <?php endforeach; ?>
          </li>
        </ul>
      <?php else: ?>
        <?= 'No Entries'; ?>
      <?php endif; ?>
    </li>

    <li>
      <h3>Bools</h3>
      <?php if (isset($bools)): ?>
        <ul>
          <li>
            <?php foreach($bools as $name => $value): ?>
              <h4><?= h($name); ?></h4>
              <pre><?= $value ? 'true' : 'false'; ?></pre>
            <?php endforeach; ?>
          </li>
        </ul>
      <?php else: ?>
        <?= 'No Entries'; ?>
      <?php endif; ?>
    </li>
  </ul>


</div>