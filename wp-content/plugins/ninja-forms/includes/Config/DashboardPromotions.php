<?php

return apply_filters( 'ninja-forms-dashboard-promotions', array(

  /*
  |--------------------------------------------------------------------------
  | Ninja Mail
  |--------------------------------------------------------------------------
  |
  */

  // @TODO: This section is deprecated. Remove at a later date.
  // 'ninja-mail' => array(
  //   'id' => 'ninja-mail',
  //   'content' => '<a href="#services"><span class="dashicons dashicons-email-alt"></span>' . __( 'Hosts are bad at sending emails. Improve the reliability of your submission emails! ', 'ninja-forms' ) . '<br /><span class="cta">' . __( 'Try our new Ninja Mail service!', 'ninja-forms' ) . '</span></a>',
  //   'script' => "
  //     setTimeout(function(){ /* Wait for services to init. */
  //       Backbone.Radio.channel( 'dashboard' ).request( 'more:service:ninja-mail' );
  //     }, 500);
  //   "
  // ),

  /*
  |--------------------------------------------------------------------------
  | SendWP
  |--------------------------------------------------------------------------
  |
  */

  'sendwp' => array(
    'id' => 'sendwp',
    'content' => '<span aria-label="SendWP. Getting WordPress email into an inbox shouldn\'t be that hard! Never miss another receipt, form submission, or any WordPress email ever again." style="cursor:pointer;width:800px;height:83px;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;background-image:url(\'' . NF_PLUGIN_URL . 'assets/img/promotions/dashboard-sendwp.png\');display:block;"></span>',
    'script' => "
      setTimeout(function(){ /* Wait for services to init. */
        var data = {
          width: 450,
          closeOnClick: 'body',
          closeOnEsc: true,
          content: '<p><h2>Frustrated that WordPress email isn’t being received?</h2><p>Form submission notifications not hitting your inbox? Some of your visitors getting form feedback via email, others not? By default, your WordPress site sends emails through your web host, which can be unreliable. Your host has spent lots of time and money optimizing to serve your pages, not send your emails.</p><h3>Sign up for SendWP today, and never deal with WordPress email issues again!</h3><p>SendWP is an email service that removes your web host from the email equation.</p><ul style=&quot;list-style-type:initial;margin-left: 20px;&quot;><li>Sends email through dedicated email service, increasing email deliverability.</li><li>Keeps form submission emails out of spam by using a trusted email provider.</li><li>On a shared web host? Don’t worry about emails being rejected because of blocked IP addresses.</li><li><strong>Only $9/month. Free 14-day trial. Cancel anytime!</strong></li></ul></p><br />',
          btnPrimary: {
            text: 'Sign me up!',
            callback: function() {
              var spinner = document.createElement('span');
              spinner.classList.add('dashicons', 'dashicons-update', 'dashicons-update-spin');
              var w = this.offsetWidth;
              this.innerHTML = spinner.outerHTML;
              this.style.width = w+'px';
              ninja_forms_sendwp_remote_install();
            }
          },
          btnSecondary: {
            text: 'Cancel',
            callback: function() {
              sendwpModal.toggleModal(false);
            }
          }
        }
        var sendwpModal = new NinjaModal(data);
      }, 500);
    "
  ),

  /*
  |--------------------------------------------------------------------------
  | Ninja Shop
  |--------------------------------------------------------------------------
  |
  */

  'ninja-shop' => array(
    'id' => 'ninja-shop',
    'content' => '<a href="https://getninjashop.com/?utm_medium=dashboard_banner&utm_source=ninja-forms&utm_campaign=Awareness" target="_blank" style="color:#FFF !important;background:#5DA54B;"><span class="dashicons dashicons-cart"></span>' . __( 'Are you frustrated with complicated eCommerce solutions?', 'ninja-forms' ) . '<br /><span class="cta">' . __( 'Start Selling Today With Ninja Shop!', 'ninja-forms' ) . '</span></a>',
    'script' => "",
  ),

));
