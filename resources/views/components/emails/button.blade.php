<table role="presentation" style="width: 100%; border: 0; cellspacing: 0; cellpadding: 0; margin: 24px 0;">
    <tr>
        <td align="{{ $align ?? 'center' }}">
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:52px;v-text-anchor:middle;width:200px;" arcsize="15%" stroke="f" fillcolor="#667eea">
                <w:anchorlock/>
                <center>
            <![endif]-->
            <a href="{{ $url }}" class="email-button"
               style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff !important; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; text-align: center;">
                {{ $slot }}
            </a>
            <!--[if mso]>
            </center>
            </v:roundrect>
            <![endif]-->
        </td>
    </tr>
</table>
