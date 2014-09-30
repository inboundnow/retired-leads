<?php

$inbound_email_templates['token-test'] = '

<h2>'. __( 'Core Tokens', 'leads' ) .'</h2>
<p>'. __( 'Admin Email Address' , 'leads' ) .':{{admin-email-address}}</p>
<p>'. __( 'Site Name' , 'leads' ) .':{{site-name}}</p>
<p>'. __( 'Site Url' , 'leads' ) .':{{site-url}}</p>
<p>'. __( 'Date-time' , 'leads' ) .': {{date-time}}</p>
<p>'. __( 'Leads URL Path' , 'leads' ) .': {{leads-urlpath}}</p>
<p>'. __( 'Landing Pages URL Path' , 'leads' ) .': {{landingpages-urlpath}}</p>

<h2>'. __( 'Lead Tokens' , 'leads' ) .'</h2>
<p>'. __( 'First Name' , 'leads' ) .': {{lead-first-name}}</p>
<p>'. __( 'Last Name' , 'leads' ) .':{{lead-last-name}}</p>
<p>'. __( 'Email' , 'leads' ) .': {{lead-email-address}}</p>
<p>'. __( 'Company Name' , 'leads' ) .': {{lead-company-name}}</p>
<p>'. __( 'Address Line 1' , 'leads' ) .': {{lead-address-line-1}}</p>
<p>'. __( 'Address Line 2' , 'leads' ) .': {{lead-address-line-2}}</p>
<p>'. __( 'City' , 'leads' ) .': {{lead-city}}</p>
<p>'. __( 'State/Region' , 'leads' ) .': {{lead-region}}</p>
<p>'. __( 'Form Name' , 'leads' ) .':{{form-name}}</p>
<p>'. __( 'Converted Page URL' , 'leads' ) .': {{source}}</p>

<h2>'. __( 'WP User Tokens' , 'leads' ) .'</h2>
<p>'. __( 'WordPress User ID' , 'leads' ) .': {{wp-user-id}}</p>
<p>'. __( 'WordPress User Username' , 'leads' ) .': {{wp-user-username}}</p>
<p>'. __( 'WordPress User First Name' , 'leads' ) .': {{wp-user-first-name}}</p>
<p>'. __( 'WordPress User Last Name' , 'leads' ) .': {{wp-user-last-name}}</p>
<p>'. __( 'WordPress User Password' , 'leads' ) .': {{wp-user-password}}</p>
<p>'. __( 'WordPress User Nicename' , 'leads' ) .': {{wp-user-nicename}}</p>
<p>'. __( 'WordPress User Display Name' , 'leads' ) .': {{wp-user-displayname}}</p>
<p>'. __( 'WordPress User Gravatar URL' , 'leads' ) .': {{wp-user-gravatar-url}}</p>


<h2>'. __( 'WP Post Tokens' , 'leads' ) .'</h2>
<p>'. __( 'WordPress Post ID' , 'leads' ) .': {{wp-post-id}}</p>
<p>'. __( 'WordPress Post Title' , 'leads' ) .': {{wp-post-title}}</p>
<p>'. __( 'WordPress Post URL' , 'leads' ) .': {{wp-post-url}}</p>
<p>'. __( 'WordPress Post Content' , 'leads' ) .': {{wp-post-content}}</p>
<p>'. __( 'WordPress Post Excerpt' , 'leads' ) .': {{wp-post-excerpt}}</p>


<h2>'. __( 'WP Comment Tokens' , 'leads' ) .'</h2>
<p>'. __( 'WordPress Comment ID' , 'leads' ) .': {{wp-comment-id}}</p>
<p>'. __( 'WordPress Comment URL' , 'leads' ) .': {{wp-comment-url}}</p>
<p>'. __( 'WordPress Comment Author' , 'leads' ) .': {{wp-comment-author}}</p>
<p>'. __( 'WordPress Comment Author Email' , 'leads' ) .': {{wp-comment-author-email}}</p>
<p>'. __( 'WordPress Comment Author IP' , 'leads' ) .': {{wp-comment-author-ip}}</p>
<p>'. __( 'WordPress Comment Content' , 'leads' ) .': {{wp-comment-content}}</p>
<p>'. __( 'WordPress Comment Date' , 'leads' ) .': {{wp-comment-date}}</p>
<p>'. __( 'WordPress Comment Karma' , 'leads' ) .': {{wp-comment-karma}}</p>


';