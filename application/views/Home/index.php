<h1 id="desafio">Desafio</h1>
<ul>
    <li><code>PHP 7.3</code></li>
    <li><code>CodeIgniter 3</code></li>
    <li><code>MySQL</code></li>
</ul>
<h2 id="demonstra-o">Banco de Dados</h2>
<ul>
    <li><code>SQL</code>: /documentação/bd.sql</li>
    <li><code>Diagrama ER</code>: /documentação/Diagrama ER.jpeg</li>
</ul>
<h2 id="demonstra-o">Demonstração Online</h2>
<pre><code>    <span class="hljs-selector-tag">BASE</span> <span class="hljs-selector-tag">URL</span>: <a href="http://65.21.123.125/desafio/">http://65.21.123.125/desafio/</a>
</code></pre><h2 id="documenta-o-api">Documentação API</h2>
<p><code>POST</code> <strong>/conta/cadastrar</strong></p>
<pre><code>Realiza o cadastro de uma conta.
</code></pre><ul>
    <li><code>nome</code>  :  <em><strong>string</strong></em></li>
    <li><code>cpf</code> : <em><strong>numeric</strong></em></li>
</ul>
</br>
<hr>
<p><code>GET</code> <strong>/conta/listar/{id_conta}</strong></p>
<pre><code>Exibe <span class="hljs-keyword">as</span> informações <span class="hljs-keyword">de</span> uma conta.
</code></pre>
<ul>
    <li><code>id_conta</code> : <em><strong>numeric</strong></em></li>
</ul>
</br>
<hr>
<p><code>POST</code> <strong>/deposito</strong></p>
<pre><code>Realiza <span class="hljs-selector-tag">a</span> operação de deposito <span class="hljs-selector-tag">em</span> uma conta informada.
</code></pre><ul>
    <li><code>conta</code> : <em><strong>numeric</strong></em></li>
    <li><code>valor</code> : <em><strong>decimal</strong></em>  <code>(format: 00000.00)</code></li>
    <li><code>moeda</code> : <em><strong>string</strong></em> <code>(format: XXX)</code></li>
</ul>
</br>
<hr>
<p><code>POST</code> <strong>/saque</strong></p>
<pre><code>Realiza <span class="hljs-selector-tag">a</span> operação de saque <span class="hljs-selector-tag">em</span> uma conta informada.
</code></pre><ul>
    <li><code>conta</code> : <em><strong>numeric</strong></em></li>
    <li><code>valor</code> : <em><strong>decimal</strong></em>  <code>(format: 00000.00)</code></li>
    <li><code>moeda</code> : <em><strong>string</strong></em> <code>(format: XXX)</code></li>
</ul>
</br>
<hr>
<p><code>GET</code> <strong>/saldo/{id_conta}</strong></p>
<pre><code>Exibe os saldos <span class="hljs-selector-tag">em</span> uma conta informada.
</code></pre><ul>
    <li><code>id_conta</code> : <em><strong>numeric</strong></em></li>
</ul>
</br>
<hr>
<p><code>GET</code> <strong>/saldo/{id_conta}/{moeda}</strong></p>
<pre><code>Exibe o saldo <span class="hljs-keyword">de</span> uma moeda especificada <span class="hljs-keyword">de</span> uma conta informada.
</code></pre><ul>
    <li><code>id_conta</code> : <em><strong>numeric</strong></em></li>
    <li><code>moeda</code> : <em><strong>string</strong></em> <code>(format: XXX)</code></li>
</ul>
</br>
<hr>
<p><code>GET</code> <strong>/extrato/{id_conta}</strong></p>
<pre><code>Exibe o extrato de uma conta informada.
</code></pre>
<ul>
    <li><code>id_conta</code> : <em><strong>numeric</strong></em></li>
</ul>
</br>
<hr>
<p><code>GET</code> <strong>/extrato/{id_conta}/{data_inicial}/{data_final}</strong></p>
<pre><code>Exibe o extrato de uma conta informada <span class="hljs-selector-tag">em</span> um determinado período.
</code></pre><ul>
    <li><code>id_conta</code> : <em><strong>numeric</strong></em></li>
    <li><code>data_incial</code> : <em><strong>date</strong></em> <code>(format: dd-mm-yyy)</code></li>
    <li><code>data_final</code> :  <em><strong>date</strong></em> <code>(format: dd-mm-yyy)</code></li>
</ul>
</br>