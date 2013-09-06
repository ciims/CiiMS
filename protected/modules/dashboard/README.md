## CiiMS Dashboard

#### Compiling CSS Assets

In order to sanely build CSS assets, this directory utilizes _grunt-cli_ to build the minified and unminified css assets.

CSS to be modified is located in _src_, and the final CSS file is written out to _assets/css/dashboard.[min.]css_. Do not modify any files in the root of css folder

Running grunt should be done as follows when editing

    npm install
    grunt watch
