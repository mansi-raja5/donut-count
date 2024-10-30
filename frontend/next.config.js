module.exports = {
  env: {
    backend_url:
      process.env.NODE_ENV === "production"
        ? "https://mansiraja.tech/"
        : "https://mansiraja.tech/",
  },
};
