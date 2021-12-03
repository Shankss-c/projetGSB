// Database initialization
let url = process.env.DATABASE_URL;
let ssl = !!process.env.DATABASE_SSL;
if(ssl) {
    url = url + '?ssl=true';
}
require('sqlagent/pg').init(url);

