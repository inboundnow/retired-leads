<?php

$inbound_email_templates['token-test'] = '

<h2>'. __( 'Core Tokens', 'cta' ) .'</h2>
<p>'. __( 'Admin Email Address' , 'cta' ) .':{{admin-email-address}}</p>
<p>'. __( 'Site Name' , 'cta' ) .':{{site-name}}</p>
<p>'. __( 'Site Url' , 'cta' ) .':{{site-url}}</p>
<p>'. __( 'Date-time' , 'cta' ) .': {{date-time}}</p>
<p>'. __( 'Leads URL Path' , 'cta' ) .': {{leads-urlpath}}</p>
<p>'. __( 'Landing Pages URL Path' , 'cta' ) .': {{landingpages-urlpath}}</p>

<h2>'. __( 'Lead Tokens' , 'cta' ) .'</h2>
<p>'. __( 'First Name' , 'cta' ) .': {{lead-first-name}}</p>
<p>'. __( 'Last Name' , 'cta' ) .':{{lead-last-name}}</p>
<p>'. __( 'Email' , 'cta' ) .': {{lead-email-address}}</p>
<p>'. __( 'Company Name' , 'cta' ) .': {{lead-company-name}}</p>
<p>'. __( 'Address Line 1' , 'cta' ) .': {{lead-address-line-1}}</p>
<p>'. __( 'Address Line 2' , 'cta' ) .': {{lead-address-line-2}}</p>
<p>'. __( 'City' , 'cta' ) .': {{lead-city}}</p>
<p>'. __( 'State/Region' , 'cta' ) .': {{lead-region}}</p>
<p>'. __( 'Form Name' , 'cta' ) .':{{form-name}}</p>
<p>'. __( 'Converted Page URL' , 'cta' ) .': {{source}}</p>

<h2>'. __( 'WP User Tokens' , 'cta' ) .'</h2>
<p>'. __( 'WordPress User ID' , 'cta' ) .': {{wp-user-id}}</p>
<p>'. __( 'WordPress User Username' , 'cta' ) .': {{wp-user-username}}</p>
<p>'. __( 'WordPress User First Name' , 'cta' ) .': {{wp-user-first-name}}</p>
<p>'. __( 'WordPress User Last Name' , 'cta' ) .': {{wp-user-last-name}}</p>
<p>'. __( 'WordPress User Password' , 'cta' ) .': {{wp-user-password}}</p>
<p>'. __( 'WordPress User Nicename' , 'cta' ) .': {{wp-user-nicename}}</p>
<p>'. __( 'WordPress User Display Name' , 'cta' ) .': {{wp-user-displayname}}</p>
<p>'. __( 'WordPress User Gravatar URL' , 'cta' ) .': {{wp-user-gravatar-url}}</p>


<h2>'. __( 'WP Post Tokens' , 'cta' ) .'</h2>
<p>'. __( 'WordPress Post ID' , 'cta' ) .': {{wp-post-id}}</p>
<p>'. __( 'WordPress Post Title' , 'cta' ) .': {{wp-post-title}}</p>
<p>'. __( 'WordPress Post URL' , 'cta' ) .': {{wp-post-url}}</p>
<p>'. __( 'WordPress Post Content' , 'cta' ) .': {{wp-post-content}}</p>
<p>'. __( 'WordPress Post Excerpt' , 'cta' ) .': {{wp-post-excerpt}}</p>


<h2>'. __( 'WP Comment Tokens' , 'cta' ) .'</h2>
<p>'. __( 'WordPress Comment ID' , 'cta' ) .': {{wp-comment-id}}</p>
<p>'. __( 'WordPress Comment URL' , 'cta' ) .': {{wp-comment-url}}</p>
<p>'. __( 'WordPress Comment Author' , 'cta' ) .': {{wp-comment-author}}</p>
<p>'. __( 'WordPress Comment Author Email' , 'cta' ) .': {{wp-comment-author-email}}</p>
<p>'. __( 'WordPress Comment Author IP' , 'cta' ) .': {{wp-comment-author-ip}}</p>
<p>'. __( 'WordPress Comment Content' , 'cta' ) .': {{wp-comment-content}}</p>
<p>'. __( 'WordPress Comment Date' , 'cta' ) .': {{wp-comment-date}}</p>
<p>'. __( 'WordPress Comment Karma' , 'cta' ) .': {{wp-comment-karma}}</p>


';