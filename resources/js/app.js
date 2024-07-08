import './bootstrap';

const cors = require('cors'); 
var app = express();
app.use(cors({
  origin: "*",
  })
);
