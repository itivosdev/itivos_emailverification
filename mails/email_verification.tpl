{extends file='admin_views/v1/mails/mail.tpl'}
{if isset($data.title)}
	{block name=title}{$data.title}{/block}
{/if}
{block name=main}
	<p>
		{l s='Hola' mod='theme.front.email'} {$data.fullname}, 
	</p>
	<p>
		Bienvenido, para activar tu cuenta y poder acceder a la plataforma, por favor valida tu correo electrónico haciendo clic en el siguiente enlace:
	</p>
	<p>
		<a href="{$data.validation_link}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block;">Validar mi correo</a>
	</p>
	<p>
		Si no puedes hacer clic en el enlace, copia y pega la siguiente URL en tu navegador: <br>
		{$data.validation_link}
	</p>
	<div class="divider"></div>
{/block}
