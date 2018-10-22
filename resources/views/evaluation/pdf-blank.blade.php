<head>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Blank Eval</title>
  <style type="text/css">
        body { font-family: 'Roboto',Arial, sans-serif; line-height: 0.9em; margin: 0; padding:0;}
        h1,h2,h3,h4,h5 { font-family: 'Roboto', sans-serif; color:#333; font-weight: 500}
        img { vertical-align: middle; }
        .img-responsive,.thumbnail > img,.thumbnail a > img, .carousel-inner > .item > img,.carousel-inner > .item > a > img {
          display: block;
          max-width: 100%;
          height: auto;
        }
        .img-rounded {
          border-radius: 6px;
        }
        .img-thumbnail {
          display: inline-block;
          max-width: 100%;
          height: auto;
          padding: 4px;
          line-height: 1.42857143;
          background-color: #fff;
          border: 1px solid #ddd;
          border-radius: 4px;
          -webkit-transition: all .2s ease-in-out;
               -o-transition: all .2s ease-in-out;
                  transition: all .2s ease-in-out;
        }
        .img-circle {
          border-radius: 50%;
        }
        hr {
          margin-top: 20px;
          margin-bottom: 20px;
          border: 0;
          border-top: 1px solid #eee;
        }
        .sr-only {
          position: absolute;
          width: 1px;
          height: 1px;
          padding: 0;
          margin: -1px;
          overflow: hidden;
          clip: rect(0, 0, 0, 0);
          border: 0;
        }
        .sr-only-focusable:active,
        .sr-only-focusable:focus {
          position: static;
          width: auto;
          height: auto;
          margin: 0;
          overflow: visible;
          clip: auto;
        }
        [role="button"] {
          cursor: pointer;
        }

        h1,
        .h1,
        h2,
        .h2,
        h3,
        .h3 {
          margin-top: 20px;
          margin-bottom: 10px;
        }
        h1 small,
        .h1 small,
        h2 small,
        .h2 small,
        h3 small,
        .h3 small,
        h1 .small,
        .h1 .small,
        h2 .small,
        .h2 .small,
        h3 .small,
        .h3 .small {
          font-size: 65%;
        }
        h4,
        .h4,
        h5,
        .h5,
        h6,
        .h6 {
          margin-top: 10px;
          margin-bottom: 10px;
        }
        h4 small,
        .h4 small,
        h5 small,
        .h5 small,
        h6 small,
        .h6 small,
        h4 .small,
        .h4 .small,
        h5 .small,
        .h5 .small,
        h6 .small,
        .h6 .small {
          font-size: 75%;
        }
        h1,
        .h1 {
          font-size: 36px;
        }
        h2,
        .h2 {
          font-size: 30px;
        }
        h3,
        .h3 {
          font-size: 24px;
        }
        h4,
        .h4 {
          font-size: 18px;
        }
        h5,
        .h5 {
          font-size: 14px;
        }
        h6,
        .h6 {
          font-size: 12px;
        }
        p {
          margin: 0 0 10px;
        }
        .lead {
          margin-bottom: 20px;
          font-size: 16px;
          font-weight: 300;
          line-height: 1.4;
        }

          .box {
          position: relative;
          border-radius: 3px;
          background: #ffffff;
          border-top: 3px solid #d2d6de;
          margin-bottom: 20px;
          width: 100%;
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }
        .box.box-primary {
          border-top-color: #3c8dbc;
        }
        .box.box-pink {
          border-top-color: #ff00bf;
        }
        .box.box-pink a {font-weight:bold; color:#ff00bf;}
        .box.box-info {
          border-top-color: #00c0ef;
        }
        .box.box-danger {
          border-top-color: #dd4b39;
        }
        .box.box-warning {
          border-top-color: #f39c12;
        }
        .box.box-success {
          border-top-color: #67aa08;
        }
        .box.box-default {
          border-top-color: #d2d6de;
        }
        .box.collapsed-box .box-body,
        .box.collapsed-box .box-footer {
          display: none;
        }
        .box .nav-stacked > li {
          border-bottom: 1px solid #f4f4f4;
          margin: 0;
        }
        .box .nav-stacked > li:last-of-type {
          border-bottom: none;
        }
        .box.height-control .box-body {
          max-height: 300px;
          overflow: auto;
        }
        .box .border-right {
          border-right: 1px solid #f4f4f4;
        }
        .box .border-left {
          border-left: 1px solid #f4f4f4;
        }
        .box.box-solid {
          border-top: 0;
        }
        .box.box-solid > .box-header .btn.btn-default {
          background: transparent;
        }
        .box.box-solid > .box-header .btn:hover,
        .box.box-solid > .box-header a:hover {
          background: rgba(0, 0, 0, 0.1);
        }
        .box.box-solid.box-default {
          border: 1px solid #d2d6de;
        }
        .box.box-solid.box-default > .box-header {
          color: #444444;
          background: #d2d6de;
          background-color: #d2d6de;
        }
        .box.box-solid.box-default > .box-header a,
        .box.box-solid.box-default > .box-header .btn {
          color: #444444;
        }
        .box.box-solid.box-primary {
          border: 1px solid #3c8dbc;
        }
        .box.box-solid.box-pink {
          border: 1px solid #ff00bf;
        }
        .box.box-solid.box-pink > .box-header {
          color: #ffffff;
          background: #ff00bf;
          background-color: #ff00bf;
        }
        .box.box-solid.box-primary > .box-header {
          color: #ffffff;
          background: #3c8dbc;
          background-color: #3c8dbc;
        }
        .box.box-solid.box-primary > .box-header a,
        .box.box-solid.box-primary > .box-header .btn {
          color: #ffffff;
        }

        .box.box-solid.box-pink > .box-header a,
        .box.box-solid.box-pink > .box-header .btn {
          color: #ffffff;
        }


        .box.box-solid.box-info {
          border: 1px solid #00c0ef;
        }
        .box.box-solid.box-info > .box-header {
          color: #ffffff;
          background: #00c0ef;
          background-color: #00c0ef;
        }
        .box.box-solid.box-info > .box-header a,
        .box.box-solid.box-info > .box-header .btn {
          color: #ffffff;
        }
        .box.box-solid.box-danger {
          border: 1px solid #dd4b39;
        }
        .box.box-solid.box-danger > .box-header {
          color: #ffffff;
          background: #dd4b39;
          background-color: #dd4b39;
        }
        .box.box-solid.box-danger > .box-header a,
        .box.box-solid.box-danger > .box-header .btn {
          color: #ffffff;
        }
        .box.box-solid.box-warning {
          border: 1px solid #f39c12;
        }
        .box.box-solid.box-warning > .box-header {
          color: #ffffff;
          background: #f39c12;
          background-color: #f39c12;
        }
        .box.box-solid.box-warning > .box-header a,
        .box.box-solid.box-warning > .box-header .btn {
          color: #ffffff;
        }
        .box.box-solid.box-success {
          border: 1px solid #67aa08;
        }
        .box.box-solid.box-success > .box-header {
          color: #ffffff;
          background: #67aa08;
          background-color: #67aa08;
        }
        .box.box-solid.box-success > .box-header a,
        .box.box-solid.box-success > .box-header .btn {
          color: #ffffff;
        }
        .box.box-solid > .box-header > .box-tools .btn {
          border: 0;
          box-shadow: none;
        }
        .box.box-solid[class*='bg'] > .box-header {
          color: #fff;
        }
        .box .box-group > .box {
          margin-bottom: 5px;
        }
        .box .knob-label {
          text-align: center;
          color: #333;
          font-weight: 100;
          font-size: 12px;
          margin-bottom: 0.3em;
        }
        .box > .overlay,
        .overlay-wrapper > .overlay,
        .box > .loading-img,
        .overlay-wrapper > .loading-img {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
        }
        .box .overlay,
        .overlay-wrapper .overlay {
          z-index: 50;
          background: rgba(255, 255, 255, 0.7);
          border-radius: 3px;
        }
        .box .overlay > .fa,
        .overlay-wrapper .overlay > .fa {
          position: absolute;
          top: 50%;
          left: 50%;
          margin-left: -15px;
          margin-top: -15px;
          color: #000;
          font-size: 30px;
        }
        .box .overlay.dark,
        .overlay-wrapper .overlay.dark {
          background: rgba(0, 0, 0, 0.5);
        }
        .box-header:before,
        .box-body:before,
        .box-footer:before,
        .box-header:after,
        .box-body:after,
        .box-footer:after {
          content: " ";
          display: table;
        }
        .box-header:after,
        .box-body:after,
        .box-footer:after {
          clear: both;
        }
        .box-header {
          color: #444;
          display: block;
          padding: 10px;
          position: relative;
        }
        .box-header.with-border {
          border-bottom: 1px solid #f4f4f4;
        }
        .collapsed-box .box-header.with-border {
          border-bottom: none;
        }
        .box-header > .fa,
        .box-header > .glyphicon,
        .box-header > .ion,
        .box-header .box-title {
          display: inline-block;
          font-size: 18px;
          margin: 0;
          line-height: 1;
        }
        .box-header > .fa,
        .box-header > .glyphicon,
        .box-header > .ion {
          margin-right: 5px;
        }
        .box-header > .box-tools {
          position: absolute;
          right: 10px;
          top: 5px;
        }
        .box-header > .box-tools [data-toggle="tooltip"] {
          position: relative;
        }
        .box-header > .box-tools.pull-right .dropdown-menu {
          right: 0;
          left: auto;
        }
        .btn-box-tool {
          padding: 5px;
          font-size: 12px;
          background: transparent;
          color: #97a0b3;
        }
        .open .btn-box-tool,
        .btn-box-tool:hover {
          color: #606c84;
        }
        .btn-box-tool.btn:active {
          box-shadow: none;
        }
        .box-body {
          border-top-left-radius: 0;
          border-top-right-radius: 0;
          border-bottom-right-radius: 3px;
          border-bottom-left-radius: 3px;
          padding: 10px;
        }
        .no-header .box-body {
          border-top-right-radius: 3px;
          border-top-left-radius: 3px;
        }
        .box-body > .table {
          margin-bottom: 0;
        }
        .box-body .fc {
          margin-top: 5px;
        }
        .box-body .full-width-chart {
          margin: -19px;
        }
        .box-body.no-padding .full-width-chart {
          margin: -9px;
        }
        .box-body .box-pane {
          border-top-left-radius: 0;
          border-top-right-radius: 0;
          border-bottom-right-radius: 0;
          border-bottom-left-radius: 3px;
        }
        .box-body .box-pane-right {
          border-top-left-radius: 0;
          border-top-right-radius: 0;
          border-bottom-right-radius: 3px;
          border-bottom-left-radius: 0;
        }
        .box-footer {
          border-top-left-radius: 0;
          border-top-right-radius: 0;
          border-bottom-right-radius: 3px;
          border-bottom-left-radius: 3px;
          border-top: 1px solid #f4f4f4;
          padding: 10px;
          background-color: #ffffff;
        }
        .chart-legend {
          margin: 10px 0;
        }

          @media print {
          *,
          *:before,
          *:after {
            color: #333 !important;
            text-shadow: none !important;
            background: transparent !important;
            -webkit-box-shadow: none !important;
                    box-shadow: none !important;
          }
          a,
          a:visited {
            text-decoration: underline;
          }
          a[href]:after {
            content: " (" attr(href) ")";
          }
          abbr[title]:after {
            content: " (" attr(title) ")";
          }
          a[href^="#"]:after,
          a[href^="javascript:"]:after {
            content: "";
          }
          pre,
          blockquote {
            border: 1px solid #999;

            page-break-inside: avoid;
          }
          thead {
            display: table-header-group;
          }
          tr,
          img {
            page-break-inside: avoid;
          }
          img {
            max-width: 100% !important;
          }
          p,
          h2,
          h3 {
            orphans: 3;
            widows: 3;
          }
          h2,
          h3 {
            page-break-after: avoid;
          }
          .navbar {
            display: none;
          }
          .btn > .caret,
          .dropup > .btn > .caret {
            border-top-color: #000 !important;
          }
          .label {
            border: 1px solid #000;
          }
          .table {
            border-collapse: collapse !important;
          }
          .table td,
          .table th {
            background-color: #fff !important;
          }
          .table-bordered th,
          .table-bordered td {
            border: 1px solid #ddd !important;
          }
        }
          b,
        strong {
          font-weight: bold;
        }
        table {
          border-spacing: 0;
          border-collapse: collapse;
        }
        td,
        th {
          padding: 0;
        }
        dfn {
          font-style: italic;
        }
        h1 {
          margin: .67em 0;
          font-size: 2em;
        }
        mark {
          color: #000;
          background: #ff0;
        }
        small {
          font-size: 80%;
        }
        sub,
        sup {
          position: relative;
          font-size: 75%;
          line-height: 0;
          vertical-align: baseline;
        }
        sup {
          top: -.5em;
        }
        sub {
          bottom: -.25em;
        }
        img {
          border: 0;
        }
        svg:not(:root) {
          overflow: hidden;
        }
        figure {
          margin: 1em 40px;
        }
        hr {
          height: 0;
          -webkit-box-sizing: content-box;
             -moz-box-sizing: content-box;
                  box-sizing: content-box;
        }
        pre {
          overflow: auto;
        }
        code,
        kbd,
        pre,
        samp {
          font-family: monospace, monospace;
          font-size: 1em;
        }
        button,
        input,
        optgroup,
        select,
        textarea {
          margin: 0;
          font: inherit;
          color: inherit;
        }
        button {
          overflow: visible;
        }
        button,
        select {
          text-transform: none;
        }
        button,
        html input[type="button"],
        input[type="reset"],
        input[type="submit"] {
          -webkit-appearance: button;
          cursor: pointer;
        }
        button[disabled],
        html input[disabled] {
          cursor: default;
        }
        button::-moz-focus-inner,
        input::-moz-focus-inner {
          padding: 0;
          border: 0;
        }
          .row {
          margin-right: -15px;
          margin-left: -15px;
        }
        .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
          position: relative;
          min-height: 1px;
          padding-right: 15px;
          padding-left: 15px;
        }
        .col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 {
          float: left;
        }
        .col-xs-12 {
          width: 100%;
        }
        .col-xs-11 {
          width: 91.66666667%;
        }
        .col-xs-10 {
          width: 83.33333333%;
        }
        .col-xs-9 {
          width: 75%;
        }
        .col-xs-8 {
          width: 66.66666667%;
        }
        .col-xs-7 {
          width: 58.33333333%;
        }
        .col-xs-6 {
          width: 50%;
        }
        .col-xs-5 {
          width: 41.66666667%;
        }
        .col-xs-4 {
          width: 33.33333333%;
        }
        .col-xs-3 {
          width: 25%;
        }
        .col-xs-2 {
          width: 16.66666667%;
        }
        .col-xs-1 {
          width: 8.33333333%;
        }
        .col-xs-pull-12 {
          right: 100%;
        }
        .col-xs-pull-11 {
          right: 91.66666667%;
        }
        .col-xs-pull-10 {
          right: 83.33333333%;
        }
        .col-xs-pull-9 {
          right: 75%;
        }
        .col-xs-pull-8 {
          right: 66.66666667%;
        }
        .col-xs-pull-7 {
          right: 58.33333333%;
        }
        .col-xs-pull-6 {
          right: 50%;
        }
        .col-xs-pull-5 {
          right: 41.66666667%;
        }
        .col-xs-pull-4 {
          right: 33.33333333%;
        }
        .col-xs-pull-3 {
          right: 25%;
        }
        .col-xs-pull-2 {
          right: 16.66666667%;
        }
        .col-xs-pull-1 {
          right: 8.33333333%;
        }
        .col-xs-pull-0 {
          right: auto;
        }
        .col-xs-push-12 {
          left: 100%;
        }
        .col-xs-push-11 {
          left: 91.66666667%;
        }
        .col-xs-push-10 {
          left: 83.33333333%;
        }
        .col-xs-push-9 {
          left: 75%;
        }
        .col-xs-push-8 {
          left: 66.66666667%;
        }
        .col-xs-push-7 {
          left: 58.33333333%;
        }
        .col-xs-push-6 {
          left: 50%;
        }
        .col-xs-push-5 {
          left: 41.66666667%;
        }
        .col-xs-push-4 {
          left: 33.33333333%;
        }
        .col-xs-push-3 {
          left: 25%;
        }
        .col-xs-push-2 {
          left: 16.66666667%;
        }
        .col-xs-push-1 {
          left: 8.33333333%;
        }
        .col-xs-push-0 {
          left: auto;
        }
        .col-xs-offset-12 {
          margin-left: 100%;
        }
        .col-xs-offset-11 {
          margin-left: 91.66666667%;
        }
        .col-xs-offset-10 {
          margin-left: 83.33333333%;
        }
        .col-xs-offset-9 {
          margin-left: 75%;
        }
        .col-xs-offset-8 {
          margin-left: 66.66666667%;
        }
        .col-xs-offset-7 {
          margin-left: 58.33333333%;
        }
        .col-xs-offset-6 {
          margin-left: 50%;
        }
        .col-xs-offset-5 {
          margin-left: 41.66666667%;
        }
        .col-xs-offset-4 {
          margin-left: 33.33333333%;
        }
        .col-xs-offset-3 {
          margin-left: 25%;
        }
        .col-xs-offset-2 {
          margin-left: 16.66666667%;
        }
        .col-xs-offset-1 {
          margin-left: 8.33333333%;
        }
        .col-xs-offset-0 {
          margin-left: 0;
        }
        @media (min-width: 768px) {
          .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left;
          }
          .col-sm-12 {
            width: 100%;
          }
          .col-sm-11 {
            width: 91.66666667%;
          }
          .col-sm-10 {
            width: 83.33333333%;
          }
          .col-sm-9 {
            width: 75%;
          }
          .col-sm-8 {
            width: 66.66666667%;
          }
          .col-sm-7 {
            width: 58.33333333%;
          }
          .col-sm-6 {
            width: 50%;
          }
          .col-sm-5 {
            width: 41.66666667%;
          }
          .col-sm-4 {
            width: 33.33333333%;
          }
          .col-sm-3 {
            width: 25%;
          }
          .col-sm-2 {
            width: 16.66666667%;
          }
          .col-sm-1 {
            width: 8.33333333%;
          }
          .col-sm-pull-12 {
            right: 100%;
          }
          .col-sm-pull-11 {
            right: 91.66666667%;
          }
          .col-sm-pull-10 {
            right: 83.33333333%;
          }
          .col-sm-pull-9 {
            right: 75%;
          }
          .col-sm-pull-8 {
            right: 66.66666667%;
          }
          .col-sm-pull-7 {
            right: 58.33333333%;
          }
          .col-sm-pull-6 {
            right: 50%;
          }
          .col-sm-pull-5 {
            right: 41.66666667%;
          }
          .col-sm-pull-4 {
            right: 33.33333333%;
          }
          .col-sm-pull-3 {
            right: 25%;
          }
          .col-sm-pull-2 {
            right: 16.66666667%;
          }
          .col-sm-pull-1 {
            right: 8.33333333%;
          }
          .col-sm-pull-0 {
            right: auto;
          }
          .col-sm-push-12 {
            left: 100%;
          }
          .col-sm-push-11 {
            left: 91.66666667%;
          }
          .col-sm-push-10 {
            left: 83.33333333%;
          }
          .col-sm-push-9 {
            left: 75%;
          }
          .col-sm-push-8 {
            left: 66.66666667%;
          }
          .col-sm-push-7 {
            left: 58.33333333%;
          }
          .col-sm-push-6 {
            left: 50%;
          }
          .col-sm-push-5 {
            left: 41.66666667%;
          }
          .col-sm-push-4 {
            left: 33.33333333%;
          }
          .col-sm-push-3 {
            left: 25%;
          }
          .col-sm-push-2 {
            left: 16.66666667%;
          }
          .col-sm-push-1 {
            left: 8.33333333%;
          }
          .col-sm-push-0 {
            left: auto;
          }
          .col-sm-offset-12 {
            margin-left: 100%;
          }
          .col-sm-offset-11 {
            margin-left: 91.66666667%;
          }
          .col-sm-offset-10 {
            margin-left: 83.33333333%;
          }
          .col-sm-offset-9 {
            margin-left: 75%;
          }
          .col-sm-offset-8 {
            margin-left: 66.66666667%;
          }
          .col-sm-offset-7 {
            margin-left: 58.33333333%;
          }
          .col-sm-offset-6 {
            margin-left: 50%;
          }
          .col-sm-offset-5 {
            margin-left: 41.66666667%;
          }
          .col-sm-offset-4 {
            margin-left: 33.33333333%;
          }
          .col-sm-offset-3 {
            margin-left: 25%;
          }
          .col-sm-offset-2 {
            margin-left: 16.66666667%;
          }
          .col-sm-offset-1 {
            margin-left: 8.33333333%;
          }
          .col-sm-offset-0 {
            margin-left: 0;
          }
        }
        @media (min-width: 992px) {
          .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
            float: left;
          }
          .col-md-12 {
            width: 100%;
          }
          .col-md-11 {
            width: 91.66666667%;
          }
          .col-md-10 {
            width: 83.33333333%;
          }
          .col-md-9 {
            width: 75%;
          }
          .col-md-8 {
            width: 66.66666667%;
          }
          .col-md-7 {
            width: 58.33333333%;
          }
          .col-md-6 {
            width: 50%;
          }
          .col-md-5 {
            width: 41.66666667%;
          }
          .col-md-4 {
            width: 33.33333333%;
          }
          .col-md-3 {
            width: 25%;
          }
          .col-md-2 {
            width: 16.66666667%;
          }
          .col-md-1 {
            width: 8.33333333%;
          }
          .col-md-pull-12 {
            right: 100%;
          }
          .col-md-pull-11 {
            right: 91.66666667%;
          }
          .col-md-pull-10 {
            right: 83.33333333%;
          }
          .col-md-pull-9 {
            right: 75%;
          }
          .col-md-pull-8 {
            right: 66.66666667%;
          }
          .col-md-pull-7 {
            right: 58.33333333%;
          }
          .col-md-pull-6 {
            right: 50%;
          }
          .col-md-pull-5 {
            right: 41.66666667%;
          }
          .col-md-pull-4 {
            right: 33.33333333%;
          }
          .col-md-pull-3 {
            right: 25%;
          }
          .col-md-pull-2 {
            right: 16.66666667%;
          }
          .col-md-pull-1 {
            right: 8.33333333%;
          }
          .col-md-pull-0 {
            right: auto;
          }
          .col-md-push-12 {
            left: 100%;
          }
          .col-md-push-11 {
            left: 91.66666667%;
          }
          .col-md-push-10 {
            left: 83.33333333%;
          }
          .col-md-push-9 {
            left: 75%;
          }
          .col-md-push-8 {
            left: 66.66666667%;
          }
          .col-md-push-7 {
            left: 58.33333333%;
          }
          .col-md-push-6 {
            left: 50%;
          }
          .col-md-push-5 {
            left: 41.66666667%;
          }
          .col-md-push-4 {
            left: 33.33333333%;
          }
          .col-md-push-3 {
            left: 25%;
          }
          .col-md-push-2 {
            left: 16.66666667%;
          }
          .col-md-push-1 {
            left: 8.33333333%;
          }
          .col-md-push-0 {
            left: auto;
          }
          .col-md-offset-12 {
            margin-left: 100%;
          }
          .col-md-offset-11 {
            margin-left: 91.66666667%;
          }
          .col-md-offset-10 {
            margin-left: 83.33333333%;
          }
          .col-md-offset-9 {
            margin-left: 75%;
          }
          .col-md-offset-8 {
            margin-left: 66.66666667%;
          }
          .col-md-offset-7 {
            margin-left: 58.33333333%;
          }
          .col-md-offset-6 {
            margin-left: 50%;
          }
          .col-md-offset-5 {
            margin-left: 41.66666667%;
          }
          .col-md-offset-4 {
            margin-left: 33.33333333%;
          }
          .col-md-offset-3 {
            margin-left: 25%;
          }
          .col-md-offset-2 {
            margin-left: 16.66666667%;
          }
          .col-md-offset-1 {
            margin-left: 8.33333333%;
          }
          .col-md-offset-0 {
            margin-left: 0;
          }
        }
        @media (min-width: 1200px) {
          .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12 {
            float: left;
          }
          .col-lg-12 {
            width: 100%;
          }
          .col-lg-11 {
            width: 91.66666667%;
          }
          .col-lg-10 {
            width: 83.33333333%;
          }
          .col-lg-9 {
            width: 75%;
          }
          .col-lg-8 {
            width: 66.66666667%;
          }
          .col-lg-7 {
            width: 58.33333333%;
          }
          .col-lg-6 {
            width: 50%;
          }
          .col-lg-5 {
            width: 41.66666667%;
          }
          .col-lg-4 {
            width: 33.33333333%;
          }
          .col-lg-3 {
            width: 25%;
          }
          .col-lg-2 {
            width: 16.66666667%;
          }
          .col-lg-1 {
            width: 8.33333333%;
          }
          .col-lg-pull-12 {
            right: 100%;
          }
          .col-lg-pull-11 {
            right: 91.66666667%;
          }
          .col-lg-pull-10 {
            right: 83.33333333%;
          }
          .col-lg-pull-9 {
            right: 75%;
          }
          .col-lg-pull-8 {
            right: 66.66666667%;
          }
          .col-lg-pull-7 {
            right: 58.33333333%;
          }
          .col-lg-pull-6 {
            right: 50%;
          }
          .col-lg-pull-5 {
            right: 41.66666667%;
          }
          .col-lg-pull-4 {
            right: 33.33333333%;
          }
          .col-lg-pull-3 {
            right: 25%;
          }
          .col-lg-pull-2 {
            right: 16.66666667%;
          }
          .col-lg-pull-1 {
            right: 8.33333333%;
          }
          .col-lg-pull-0 {
            right: auto;
          }
          .col-lg-push-12 {
            left: 100%;
          }
          .col-lg-push-11 {
            left: 91.66666667%;
          }
          .col-lg-push-10 {
            left: 83.33333333%;
          }
          .col-lg-push-9 {
            left: 75%;
          }
          .col-lg-push-8 {
            left: 66.66666667%;
          }
          .col-lg-push-7 {
            left: 58.33333333%;
          }
          .col-lg-push-6 {
            left: 50%;
          }
          .col-lg-push-5 {
            left: 41.66666667%;
          }
          .col-lg-push-4 {
            left: 33.33333333%;
          }
          .col-lg-push-3 {
            left: 25%;
          }
          .col-lg-push-2 {
            left: 16.66666667%;
          }
          .col-lg-push-1 {
            left: 8.33333333%;
          }
          .col-lg-push-0 {
            left: auto;
          }
          .col-lg-offset-12 {
            margin-left: 100%;
          }
          .col-lg-offset-11 {
            margin-left: 91.66666667%;
          }
          .col-lg-offset-10 {
            margin-left: 83.33333333%;
          }
          .col-lg-offset-9 {
            margin-left: 75%;
          }
          .col-lg-offset-8 {
            margin-left: 66.66666667%;
          }
          .col-lg-offset-7 {
            margin-left: 58.33333333%;
          }
          .col-lg-offset-6 {
            margin-left: 50%;
          }
          .col-lg-offset-5 {
            margin-left: 41.66666667%;
          }
          .col-lg-offset-4 {
            margin-left: 33.33333333%;
          }
          .col-lg-offset-3 {
            margin-left: 25%;
          }
          .col-lg-offset-2 {
            margin-left: 16.66666667%;
          }
          .col-lg-offset-1 {
            margin-left: 8.33333333%;
          }
          .col-lg-offset-0 {
            margin-left: 0;
          }
        }
        table {
          background-color: transparent;
        }
        caption {
          padding-top: 8px;
          padding-bottom: 8px;
          color: #777;
          text-align: left;
        }
        th {
          text-align: left;
        }
        .table {
          width: 100%;
          max-width: 100%;
          margin-bottom: 20px;
          font-family:'Roboto';
        }
        .table > thead > tr > th,
        .table > tbody > tr > th,
        .table > tfoot > tr > th,
        .table > thead > tr > td,
        .table > tbody > tr > td,
        .table > tfoot > tr > td {
          padding: 8px;
          line-height: 0.9em; /* 1.42857143;*/
          vertical-align: top;
          border-top: 1px solid #ddd;
        }
        .table > thead > tr > th {
          vertical-align: bottom;
          border-bottom: 2px solid #ddd;
        }
        .table > caption + thead > tr:first-child > th,
        .table > colgroup + thead > tr:first-child > th,
        .table > thead:first-child > tr:first-child > th,
        .table > caption + thead > tr:first-child > td,
        .table > colgroup + thead > tr:first-child > td,
        .table > thead:first-child > tr:first-child > td {
          border-top: 0;
        }
        .table > tbody + tbody {
          border-top: 2px solid #ddd;
        }
        .table .table {
          background-color: #fff;
        }
        .table-condensed > thead > tr > th,
        .table-condensed > tbody > tr > th,
        .table-condensed > tfoot > tr > th,
        .table-condensed > thead > tr > td,
        .table-condensed > tbody > tr > td,
        .table-condensed > tfoot > tr > td {
          padding: 5px;
        }
        .table-bordered {
          border: 1px solid #ddd;
        }
        .table-bordered > thead > tr > th,
        .table-bordered > tbody > tr > th,
        .table-bordered > tfoot > tr > th,
        .table-bordered > thead > tr > td,
        .table-bordered > tbody > tr > td,
        .table-bordered > tfoot > tr > td {
          border: 1px solid #ddd;
        }
        .table-bordered > thead > tr > th,
        .table-bordered > thead > tr > td {
          border-bottom-width: 2px;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
          background-color: #f9f9f9;
        }
        .table-hover > tbody > tr:hover {
          background-color: #f5f5f5;
        }
        table col[class*="col-"] {
          position: static;
          display: table-column;
          float: none;
        }
        table td[class*="col-"],
        table th[class*="col-"] {
          position: static;
          display: table-cell;
          float: none;
        }
        .table > thead > tr > td.active,
        .table > tbody > tr > td.active,
        .table > tfoot > tr > td.active,
        .table > thead > tr > th.active,
        .table > tbody > tr > th.active,
        .table > tfoot > tr > th.active,
        .table > thead > tr.active > td,
        .table > tbody > tr.active > td,
        .table > tfoot > tr.active > td,
        .table > thead > tr.active > th,
        .table > tbody > tr.active > th,
        .table > tfoot > tr.active > th {
          background-color: #f5f5f5;
        }
        .table-hover > tbody > tr > td.active:hover,
        .table-hover > tbody > tr > th.active:hover,
        .table-hover > tbody > tr.active:hover > td,
        .table-hover > tbody > tr:hover > .active,
        .table-hover > tbody > tr.active:hover > th {
          background-color: #e8e8e8;
        }
        .table > thead > tr > td.success,
        .table > tbody > tr > td.success,
        .table > tfoot > tr > td.success,
        .table > thead > tr > th.success,
        .table > tbody > tr > th.success,
        .table > tfoot > tr > th.success,
        .table > thead > tr.success > td,
        .table > tbody > tr.success > td,
        .table > tfoot > tr.success > td,
        .table > thead > tr.success > th,
        .table > tbody > tr.success > th,
        .table > tfoot > tr.success > th {
          background-color: #dff0d8;
        }
        .table-hover > tbody > tr > td.success:hover,
        .table-hover > tbody > tr > th.success:hover,
        .table-hover > tbody > tr.success:hover > td,
        .table-hover > tbody > tr:hover > .success,
        .table-hover > tbody > tr.success:hover > th {
          background-color: #d0e9c6;
        }
        .table > thead > tr > td.info,
        .table > tbody > tr > td.info,
        .table > tfoot > tr > td.info,
        .table > thead > tr > th.info,
        .table > tbody > tr > th.info,
        .table > tfoot > tr > th.info,
        .table > thead > tr.info > td,
        .table > tbody > tr.info > td,
        .table > tfoot > tr.info > td,
        .table > thead > tr.info > th,
        .table > tbody > tr.info > th,
        .table > tfoot > tr.info > th {
          background-color: #d9edf7;
        }
        .table-hover > tbody > tr > td.info:hover,
        .table-hover > tbody > tr > th.info:hover,
        .table-hover > tbody > tr.info:hover > td,
        .table-hover > tbody > tr:hover > .info,
        .table-hover > tbody > tr.info:hover > th {
          background-color: #c4e3f3;
        }
        .table > thead > tr > td.warning,
        .table > tbody > tr > td.warning,
        .table > tfoot > tr > td.warning,
        .table > thead > tr > th.warning,
        .table > tbody > tr > th.warning,
        .table > tfoot > tr > th.warning,
        .table > thead > tr.warning > td,
        .table > tbody > tr.warning > td,
        .table > tfoot > tr.warning > td,
        .table > thead > tr.warning > th,
        .table > tbody > tr.warning > th,
        .table > tfoot > tr.warning > th {
          background-color: #fcf8e3;
        }
        .table-hover > tbody > tr > td.warning:hover,
        .table-hover > tbody > tr > th.warning:hover,
        .table-hover > tbody > tr.warning:hover > td,
        .table-hover > tbody > tr:hover > .warning,
        .table-hover > tbody > tr.warning:hover > th {
          background-color: #faf2cc;
        }
        .table > thead > tr > td.danger,
        .table > tbody > tr > td.danger,
        .table > tfoot > tr > td.danger,
        .table > thead > tr > th.danger,
        .table > tbody > tr > th.danger,
        .table > tfoot > tr > th.danger,
        .table > thead > tr.danger > td,
        .table > tbody > tr.danger > td,
        .table > tfoot > tr.danger > td,
        .table > thead > tr.danger > th,
        .table > tbody > tr.danger > th,
        .table > tfoot > tr.danger > th {
          background-color: #f2dede;
        }
        .table-hover > tbody > tr > td.danger:hover,
        .table-hover > tbody > tr > th.danger:hover,
        .table-hover > tbody > tr.danger:hover > td,
        .table-hover > tbody > tr:hover > .danger,
        .table-hover > tbody > tr.danger:hover > th {
          background-color: #ebcccc;
        }
        .table-responsive {
          min-height: .01%;
          overflow-x: auto;
        }
        @media screen and (max-width: 767px) {
          .table-responsive {
            width: 100%;
            margin-bottom: 15px;
            overflow-y: hidden;
            -ms-overflow-style: -ms-autohiding-scrollbar;
            border: 1px solid #ddd;
          }
          .table-responsive > .table {
            margin-bottom: 0;
          }
          .table-responsive > .table > thead > tr > th,
          .table-responsive > .table > tbody > tr > th,
          .table-responsive > .table > tfoot > tr > th,
          .table-responsive > .table > thead > tr > td,
          .table-responsive > .table > tbody > tr > td,
          .table-responsive > .table > tfoot > tr > td {
            white-space: nowrap;
          }
          .table-responsive > .table-bordered {
            border: 0;
          }
          .table-responsive > .table-bordered > thead > tr > th:first-child,
          .table-responsive > .table-bordered > tbody > tr > th:first-child,
          .table-responsive > .table-bordered > tfoot > tr > th:first-child,
          .table-responsive > .table-bordered > thead > tr > td:first-child,
          .table-responsive > .table-bordered > tbody > tr > td:first-child,
          .table-responsive > .table-bordered > tfoot > tr > td:first-child {
            border-left: 0;
          }
          .table-responsive > .table-bordered > thead > tr > th:last-child,
          .table-responsive > .table-bordered > tbody > tr > th:last-child,
          .table-responsive > .table-bordered > tfoot > tr > th:last-child,
          .table-responsive > .table-bordered > thead > tr > td:last-child,
          .table-responsive > .table-bordered > tbody > tr > td:last-child,
          .table-responsive > .table-bordered > tfoot > tr > td:last-child {
            border-right: 0;
          }
          .table-responsive > .table-bordered > tbody > tr:last-child > th,
          .table-responsive > .table-bordered > tfoot > tr:last-child > th,
          .table-responsive > .table-bordered > tbody > tr:last-child > td,
          .table-responsive > .table-bordered > tfoot > tr:last-child > td {
            border-bottom: 0;
          }
        }
  </style>

  <!-- Page script -->
<script>
  
  $(function () {
   'use strict';
   window.print();
   return false;
 });
  </script>



</head>

<body onload="javascript:window.print(); return false;">
  <!-- Content Header (Page header) -->

      
     

      <table class="table">
                            <tr>
                              <td>
                               
                                  <img src="{{asset('public/img/useravatar.png')}}" class="user-image pull-left" alt="Employee Image" width="80" style="margin-right:10px;">


                                  <h5 style="line-height:0.8em"> Name: </h5>
                                  <h5 class="widget-user-desc" ><span style='font-style:italic'>Job title: </span><br/>
                                 <strong> Program/Department: </strong> </h5>
                                 
                                 <p style="font-size:0.7em"><br/>Date Hired: <small> ________________________</small> <br/>
                                  Date Evaluated: <small> ________________________</small> <br/>
                                   Evaluated by:  ________________________</p>

                               

                              </td>
                              <td align="right">
                                <h4 class="text-center"><img src="{{asset('public/img/new-oam-footer-logo.png')}}" style="float:left; padding-left:-20px"  />
                                   Contract Extension Evaluation<br/>
                                  <small>Evaluation Period: <strong> </strong> </small>

                               
                                </h4>
                                <h1 style="line-height:0.8; background-color:#67aa08; color:#fff; width:200px; padding-top:5px; float:right" id="overallScore"  data-score="{{$evalForm->overAllScore}}">
                                  <span style="font-size:0.6em;" class="text-gray"><small>Performance Rating: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small> </span>  %<br/></h1>
                                  <div style="clear:both"></div>

                                  <table width="100%">
                                    <tr>
                                      <td valign="top" width="45%">
                                        <table style="margin: 0 auto; font-size:0.6em; font-family:'Roboto', Arial, sans-serif; border:1px dotted #666; left-padding:15px" width="80%" cellpadding="1">
                                          <tr>
                                            <td colspan="2"  align="center">Salary Increase Metrics</td>
                                          </tr>
                                          <tr>
                                            <td>&nbsp;&nbsp;&nbsp;<strong>Total Score</strong></td>
                                            <td><strong>Salary Increase</strong></td>
                                          </tr>
                                          <tr>
                                            <td @if ($evalForm->overAllScore <= 100.0 && $evalForm->overAllScore >= 97.5)  @endif>&nbsp;&nbsp;&nbsp;100 - 97.5</td>
                                            <td @if ($evalForm->overAllScore <= 100.0 && $evalForm->overAllScore >= 97.5)   @endif align="center">5%</td>
                                            
                                          </tr>
                                          <tr>
                                            <td @if ($evalForm->overAllScore <= 97.4 && $evalForm->overAllScore >= 89.5)   @endif>&nbsp;&nbsp;&nbsp;97.4 - 89.5</td>
                                            <td  @if ($evalForm->overAllScore <= 97.4 && $evalForm->overAllScore >= 89.5)   @endif align="center">4%</td>
                                          </tr>

                                          <tr style="border:1px dotted #67aa08">
                                            <td @if ($evalForm->overAllScore <= 89.4 && $evalForm->overAllScore >= 84.5)   @endif>&nbsp;&nbsp;&nbsp;89.4 - 84.5</td>
                                            <td @if ($evalForm->overAllScore <= 89.4 && $evalForm->overAllScore >= 84.5)  @endif align="center">3%</td>
                                          </tr>

                                          <tr>
                                            <td @if ($evalForm->overAllScore <= 84.4 && $evalForm->overAllScore >= 80.0)   @endif>&nbsp;&nbsp;&nbsp;84.4 - 80.0</td>
                                            <td @if ($evalForm->overAllScore <= 84.4 && $evalForm->overAllScore >= 80.0)   @endif align="center">2%</td>
                                          </tr>

                                          <tr>
                                            <td @if ($evalForm->overAllScore <= 79.9 && $evalForm->overAllScore >= 70.0)   @endif>&nbsp;&nbsp;&nbsp;79.9 - 70.0</td>
                                            <td @if ($evalForm->overAllScore <= 79.9 && $evalForm->overAllScore >= 70.0)   @endif align="center">1%</td>
                                          </tr>

                                          <tr>
                                            <td @if ($evalForm->overAllScore <= 69.9 )   @endif>&nbsp;&nbsp;&nbsp;69.9 below</td>
                                            <td @if ($evalForm->overAllScore <= 69.9 )   @endif  align="center">none</td>
                                          </tr>
                                          <tr><td colspan="2">&nbsp;</td></tr>

                                        </table>

                                      </td>


                                      <td style="border:1px dotted #666;">
                                        <table style="margin: 0 auto; font-size:0.6em; left-padding:15px" width="100%" cellpadding="1">
                                          <tr>
                                            <td colspan="3"  align="center">Grading Scale</td>
                                          </tr>
                             
                                            <tr>
                                              <td class="text-center">Scale</td>
                                              <td class="text-center">%</td>
                                              <td class="text-center">Status</td>
                                              
                                            </tr>
                                         
                                          
                                            @foreach ($ratingScale as $rs)
                                            <tr id="rating-{{$rs->percentage}}" class="ratingTable" data-salaryIncrease="{{$rs->increase}}">
                                              <td>{{$rs->label}}</td>
                                              <td class="text-center">{{$rs->maxRange}}</td>
                                              <td class="text-center">{{$rs->status}}</td>
                                              
                                            </tr>
                                            @endforeach

                                         
                                        </table>


                                      </td>
                                    </tr>

                                  </table>

                        
                                 
                              </td>

                            </tr>

                          </table>

                     

                              <table class="table table-striped" style="font-size:0.8em; font-family:'Roboto',Arial,sans-serif">
                                
                                <tr>
                                  <td class="text-center">Competencies</td>
                                  <td style="width:15%" class="text-center">Max. Weight</td>
                                  <td style="width: 15%"class="text-center">Weighted Score</td>
                                  <td style="width:15%"class="text-center">Scale</td>
                                </tr>
                              </table>
                                          <?php $cnt=0; $printed=0; ?>
                                          @foreach ($formEntries as $entry)

                                             @if ($cnt+1 >= count($formEntries)) <!--last entry-->

                                             @else


                                                @if ($entry['competency'] == $formEntries[$cnt+1]['competency'])
                                                <!-- ******** collapsible box ********** -->
                                                      <div class="box box-default " style="padding-top:-10px">
                                                     
                                                        

                                                        <table class="table">
                                                              <tr>
                                                                <td style="width:55%; font-size:0.7em">
                                                                   
                                                                   <h5 >{{$entry['competency']}} </h5>
                                                                   
                                                                   
                                                                </td>
                                                                <td class="text-center">
                                                                  <h5 class="text-primary"><?php $num =  ( $entry['percentage']*$ratingScale[0]->label ) / 100; echo number_format((($num/$maxScore)*100),2)  ?></h5>
                                                                </td>
                                                                @if ($employee->userType_id == 4 )
                                                                <td class="text-center "><h5 class="scores" value="0"> </h5></td>
                                                                @else

                                                                <td class="text-center "><h5 class="scores" value="0"> </h5></td>


                                                                @endif
                                                                

                                                               

                                                                <td class="text-center text-danger"><h5> </h5>
                                                                  
                                                                </td>

                                                                
                                                                
                                                              </tr>
                                                               <div style="font-size:0.5em; margin-top:-40px"><?php echo $entry['definitions'] ?></div>

                                                        </table>
                                                       

                                                      <!-- /.box-header -->
                                                      <div class="box-body">                                                        

                                                        <div style="font-style:italic; color:gray; font-size:0.6em; padding-bottom:-20px">

                                                           @if (!empty($entry['value']))
                                                                    <label>{{$entry['attribute']}}</label><br />{{$entry['value']}}
                                                                    <br/><br/>
                                                                    @endif

                                                                     @if (!empty($formEntries[$cnt+1]['value']))

                                                                     <br/><br/><label>{{$formEntries[$cnt+1]['attribute']}}</label><br/><p>{{$formEntries[$cnt+1]['value']}}</p>
                                                                    <br/>
                                                                    @endif
                                                                    

                                                        </div>
                                                      </div>
                                                      <!-- /.box-body -->
                                                    </div>
                                                    <!-- ******** end collapsible box ********** -->

                                                @else

                                                  @if ($cnt+1 >= count($formEntries)) <!--last entry-->

                                                  @else

                                                  @endif


                                                    

                                                @endif

                                                   


                                               
                                                    
                                                  <?php $cnt++;  ?>


                                            @endif
                                         
                                          @endforeach
                                        
                                          
                                <!-- ******** SUMMARY ******* -->
                                <div class="box box-default">
                                <div class="box-header">Overall Performance Summary</div>
                                <div class="box-body">
                                  <table style="font-size:0.6em; font-family:'Roboto', Arial, sans-serif;border-color:#666" border="0.5">
                                    <?php $ctr=1; $indexCtr=0; ?>
                                    @foreach ($summaries as $summary)
                                         @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr] !== "   " || $performanceSummary[$indexCtr] !== "")
                                        
                                          <tr >
                                            <td colspan="2"><strong>{{$ctr}}.  {{$summary['header']}} </strong><br/>
                                              {{ $summary['details'] }}<br/><br/></td>
                                          </tr>
                                        @endif
                                    
                                        @if ( $summary['columns'] !== null)
                                         
                                          <tr> 
                                            @foreach ($summary['columns'] as $col)
                                              @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr]!== '   ' || $performanceSummary[$indexCtr] !== '')
                                              <th class="bg-gray text-center">{{$col->name}} </th>
                                              @endif
                                            @endforeach
                                          </tr>
                                          
                                          <tr>
                                             @foreach ($summary['columns'] as $col)

                                               @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr]!== '   ' || $performanceSummary[$indexCtr] !== '')
                                            <td>{{$performanceSummary[$indexCtr]}}<p>&nbsp;</p></td>
                                              @endif
                                            <?php $indexCtr++;?>
                                            
                                            @endforeach
                                          </tr>
                                         
                                        @endif

                                        @if ( $summary['rows'] !== null)

                                       
                                             @foreach ($summary['rows'] as $row)

                                                 @if (!empty($performanceSummary[$indexCtr]) || $performanceSummary[$indexCtr]!== "    " || $performanceSummary[$indexCtr] !== "")
                                                    <tr>
                                                     
                                                      <th class="text-right" valign="middle">{{$row->name}} </th>
                                                      <td>{{$performanceSummary[$indexCtr]}}<p>&nbsp;</p></td>
                                                      

                                                      
                                                    </tr>
                                                @endif

                                                <?php $indexCtr++;?>

                                            @endforeach
                                     
                                        
                                        @endif
                                    
                                    
                                    <?php $ctr++; ?>
                                   
                                    @endforeach
                                  </table>

                                </div>
                              </div>

                                <!- ********* END SUMMARY ********** -->   

                                <h5>Signed by:</h5>   
                                <table border="0" width="100%">
                                  <tr>
                                    <td>
                                      <p style="font-size:0.8em"><br/>
                                  Employee : <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>_____________________________
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>Employee Name<br/>
                                  Date : </p>
                                    </td>

                                    <td>
                                       <p style="font-size:0.8em"><br/>
                                  Evaluator :<br/>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>_____________________________
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>Evaluator's Name<br/>
                                  Date : </p>
                                    </td>
                                  </tr>
                                </table>  

                                

                                 
                              

                              

                              
                      





           
          </div><!-- end row -->

       
     </section>
          </body>



