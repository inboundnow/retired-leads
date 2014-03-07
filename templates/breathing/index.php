<style type="text/css">
  #inbound-box {
    margin: 20px auto;
    width: 100%;
    height: 300px;
    background-color: #EEE;
    padding: 0 20px;
    white-space: nowrap;
    animation: resize 5s infinite;
    -webkit-animation:  resize 5s infinite; /* Safari 4+ */
      -moz-animation:    resize 5s infinite; /* Fx 5+ */
      -o-animation:      resize 5s infinite; /* Opera 12+ */
      overflow: hidden;
  }
  #inbound-center {
    vertical-align: middle;
    display: inline-block;
    white-space: normal;
  }
  #inbound-box:after {
    content: "";
    width: 1px;
    height: 100%;
    vertical-align: middle;
    display: inline-block;
    margin-right: -10px;
  }
  #inbound-content-area, #inbound-content-area p {
    color: #{{content-text-color}};
  }
  h1#inbound-heading-1 {
    margin-top: 35px;
  }
  h2#inbound-heading-2 {
    color:#{{sub-text-color}};
  }
</style>
<div id="inbound-box">
  <div id="inbound-center">
    <h1 id="inbound-heading-1">
      {{header-text}}

    </h1>
    <h2 id="inbound-heading-2">
     {{sub-header-text}}
    </h2>
    <div id="inbound-content-area">
     {{content-area}}
    </div>

  </div>
</div>