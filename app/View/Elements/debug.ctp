<div class="debug-area">
  <h2>Debug Area</h2>
  <ul>
    <li>
        <h3>Arrays</h3>

        <?php if (isset($arrays)): ?>
          <ul>
            <?php foreach($arrays as $name => $array): ?>
              <li>
                <details open>
                  <summary><?= h($name); ?></summary>
                  <?php if (!is_null($array)): ?>
                    <pre><?php print_r($array); ?></pre>
                  <?php else: ?>
                    <p>NULL</p>
                  <?php endif; ?>
                </details>
              </li>
            <?php endforeach; ?>
            <li>
              <details open>
                <summary>$this->request->data</summary>
                <?php if (!isset($this->request->data)): ?>
                  <pre><?php print_r($this->request->data); ?></pre>
                <?php else: ?>
                  <p>NULL</p>
                <?php endif; ?>
              </details>
            </li>
            <li>
              <details open>
                <summary>$this->Session->read()</summary>
                <?php if (!is_null($this->Session->read())): ?>
                  <pre><?php print_r($this->Session->read()); ?></pre>
                  <?php else: ?>
                    <p>NULL</p>
                <?php endif; ?>
              </details>
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