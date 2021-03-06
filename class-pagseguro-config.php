<?php

/**
 *
 */
class VHR_PagSeguro
{
  protected $paymentsMethods = array(
    array(
      'title' => 'Cartão de Crédito',
      'value' => 'CREDIT_CARD'
    ),
    array(
      'title' => 'Boleto',
      'value' => 'BOLETO'
    ),
    array(
      'title' => 'Débito online',
      'value' => 'EFT'
    ),
    array(
      'title' => 'Saldo',
      'value' => 'BALANCE'
    ),
    array(
      'title' => 'Depósito em conta',
      'value' => 'DEPOSIT'
    ),
  );

  function __construct()
  {
    add_action('admin_menu', array( $this, 'setup_page'));
    add_action('admin_post_pagseguro_options', array($this, 'save_pagseguro'));
  }

  function setup_page(){
    add_submenu_page("edit.php?post_type=eventos", "Configurações PagSeguro", "Configurações PagSeguro", "manage_options", "pagseguro", array( $this, "pagseguro_setup_page"));
  }

  public function add_pagseguro_init(){
    $credentials = array(
      'email' => get_option('email_pagseguro','email@email.com.br'),
      'token' => get_option('token_pagseguro', '')
    );

    $sandbox = get_option('sandbox', 0);

    \PagSeguro\Library::initialize();
    \PagSeguro\Library::cmsVersion()->setName("VHR")->setRelease("1.0.0");
    \PagSeguro\Library::moduleVersion()->setName("VHR")->setRelease("1.0.0");

    if($sandbox == 1){
      \PagSeguro\Configuration\Configure::setEnvironment("sandbox");
      // $this->pagseguro_script_callback($sandbox);
    } else {
      \PagSeguro\Configuration\Configure::setEnvironment('production');
      // $this->pagseguro_script_callback($sandbox);
    }

    \PagSeguro\Configuration\Configure::setAccountCredentials($credentials['email'], $credentials['token']);

    \PagSeguro\Configuration\Configure::setCharset('UTF-8');// UTF-8 or ISO-8859-1

  }

  function pagseguro_script_callback($sandbox){
    if(1 == $sandbox){
      ?>
      <script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js">
      </script>
      <?php
    } else {
      ?>
      <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js">
      </script>
      <?php
    }

  }

  function pagseguro_setup_page(){
    ?>
      <div class="wrap">
        <h1><?php echo get_admin_page_title(); ?></h1>
        <?php if( filter_input(INPUT_GET, 'notice') === 'success'): ?>
        <div class="notice notice-success">
          <p>
            Configurações atualizadas com sucesso.
          </p>
        </div>
        <?php elseif( filter_input(INPUT_GET, 'notice') === 'error'): ?>
          <div class="notice notice-error">
            <p>
              Erro ao atualizar as configurações.
            </p>
          </div>
        <?php endif; ?>
        <p>Configurações do PagSeguro para configurar o sistema de pagamentos.</p>
        <form action="admin-post.php" method="post">
          <input type="hidden" name="action" value="pagseguro_options">
          <?php wp_nonce_field('pagseguro_options'); ?>
          <table class="form-table">
              <tr>
                <th scope="row">
                  <label for="email">Email da Conta</label>
                </th>
                <td>
                  <input class="regular-text" type="email" name="email" id="email" value="<?php echo get_option('email_pagseguro'); ?>">
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="token">Token da Conta</label>
                </th>
                <td>
                  <input class="regular-text" type="text" name="token" id="token" value="<?php echo get_option('token_pagseguro'); ?>">
                </td>
              </tr>
              <tr>
                <th scope="row">
                   <label for="sandbox">Usar Sandbox?</label>
                </th>
                <td>
                  <input type="checkbox" name="sandbox" id="sandbox" value="1" <?php checked( get_option('sandbox', 1), 1 ); ?>>
                </td>
              </tr>
          </table>
          <br class="clear">
          <h2>Configurações avançadas</h2>
          <table class="form-table">
            <tr valign="top">
              <th scope="row">
                <label for="acceptPayment">Tipos de pagamento aceito</label>
              </th>
              <td>
                <fieldset>
                  <legend class="screen-reader-text"><span>Tipos de pagamento aceito</span></legend>
                  <?php foreach($this->paymentsMethods as $method):
                    $options = get_option( 'acceptPayment', array('CREDIT_CARD') );
                    $checked = checked( in_array($method['value'], $options), true, false );
                    ?>
                    <label for="<?=$method['value']?>">
                      <input type="checkbox" name="acceptPayment[]" id="<?=$method['value']?>" value="<?=$method['value']?>" <?=$checked?> disabled>
                      <span><?=$method['title']?></span>
                    </label>
                  <?php endforeach; ?>
                </fieldset>
                <p class="description">
                  Selecione ao menos um tipo.
                </p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row">
                <label for="mail_template_subject">Template do Titulo de E-mail de Resposta</label>
              </th>
              <td>
                <?php $mail_template_subject = get_option('mail_template_subject'); ?>
                <input type="text" name="mail_template_subject" id="mail_template_subject" class="large-text" value="<?=esc_html($mail_template_subject)?>" placeholder="Template do Titulo de E-mail de Resposta">
                <p class="description">
                  Tags aceitas para completar valores automaticamente no título do e-mail:<br>
                  <code>[order]</code> : Adiciona o número do pedido.<br>
                  <code>[username]</code> : Adiciona o nome completo do cliente.<br>
                  <code>[firstname]</code> : Adiciona o primeiro nome do cliente.<br>
                  <code>[event]</code> : Adiciona o título do evento.
                </p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row">
                <label for="mail_template">Template do Corpo de E-mail de Resposta</label>
              </th>
              <td>
                <?php $mail_template = get_option('mail_template'); ?>
                <?php wp_editor( $mail_template, 'mail_template' ) ?>
                <!-- <textarea id="mail_template" name="mail_template" cols="80" rows="10" class="large-text" placeholder="Template de E-mail de Resposta"><?php echo esc_textarea( $mail_template ); ?></textarea> -->
                <p class="description">
                  Tags aceitas para completar valores automaticamente no corpo do e-mail:<br>
                  <code>[barcode]</code> : Adiciona a imagem do Código de Barra no e-mail.<br>
                  <code>[order]</code> : Adiciona o número do pedido.<br>
                  <code>[username]</code> : Adiciona o nome completo do cliente.<br>
                  <code>[firstname]</code> : Adiciona o primeiro nome do cliente.<br>
                  <code>[event]</code> : Adiciona o título do evento.<br>
                  <code>[purchasevalue]</code> : Adiciona o valor total da compra.<br>
                  <code>[purchaseitems]</code> : Adiciona uma lista dos itens adquiridos da compra. <br>
                  <code>[transcationcode]</code> : Adiciona o código da transação no pagseguro.<br>
                  <code>[orderdate]</code> : Adiciona a data do pedido.<br>
                  <code>[address]</code> : Adiciona o endereço do usuário.<br>
                  <code>[numberAddress]</code> : Adiciona o número do endereço do usuário.<br>
                  <code>[compAddress]</code> : Adiciona o complemento do endereço do usuário.<br>
                  <code>[cep]</code> : Adiciona o cep do endereço do usuário.<br>
                  <code>[city]</code> : Adiciona a cidade do endereço do usuário.<br>
                  <code>[state]</code> : Adiciona o estado do endereço do usuário.<br>
                  <code>[cel]</code> : Adiciona o número do celular do usuário.
                </p>
              </td>
            </tr>
          </table>
          <?php submit_button(); ?>
        </form>
      </div>
    <?php
  }

  function save_pagseguro(){
    check_admin_referer('pagseguro_options');

    $email = sanitize_email($_POST['email']);
    $token = sanitize_text_field($_POST['token']);
    $sandbox = $_POST['sandbox'];
    $acceptPayment = $_POST['acceptPayment'];
    $mail_template = wp_kses_post($_POST['mail_template']);
    $mail_template_subject = $_POST['mail_template_subject'];

    update_option('email_pagseguro', $email);
    update_option('token_pagseguro', $token);
    update_option('sandbox', $sandbox);
    update_option('acceptPayment', array('CREDIT_CARD'));
    update_option('mail_template', $mail_template );
    update_option('mail_template_subject', $mail_template_subject );

    $redirect = add_query_arg('notice', 'success', wp_get_referer());

    wp_redirect($redirect);
  }

}

new VHR_PagSeguro();
