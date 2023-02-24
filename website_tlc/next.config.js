const withPlugins = require("next-compose-plugins");
const withImages = require("next-images");
const withFonts = require("next-fonts");
const webpack = require("webpack");
const path = require("path");

// process.env.NODE_ENV will be set to 'production if npm run build-static

if (process.env.NODE_ENV === "development") {
  module.exports = withPlugins([withImages, withFonts], {
    basePath: "",
    trailingSlash: true,
    images: {
      disableStaticImages: true,
    },
    webpack(config, options) {
      config.resolve.modules.push(path.resolve("./"));
      return config;
    },
    env: {
      DOMAIN: "http://localhost:3000",
      PHP_ENDPOINT: "http://localhost:8888/index.php",
      IMG_BASE_URL: "http://localhost:3000/_assets",
      PDF_ENDPOINT: "http://localhost:8888/pdf/generate_pdf.php",
      DATA_BASE_URL: "http://localhost:3000/_data",
    },
  });
} else {
  module.exports = withPlugins([withImages, withFonts], {
    basePath: "",
    trailingSlash: true,
    images: {
      disableStaticImages: true,
    },
    webpack(config, options) {
      config.resolve.modules.push(path.resolve("./"));
      return config;
    },
    env: {
      DOMAIN: "https://thelogconnection.com",
      PHP_ENDPOINT: "https://thelogconnection.com/_php/index.php",
      IMG_BASE_URL: "https://thelogconnection.com/_assets",
      PDF_ENDPOINT: "https://thelogconnection.com/_php/pdf/generate_pdf.php",
      DATA_BASE_URL: "https://thelogconnection.com/_data",
    },
  });
}
