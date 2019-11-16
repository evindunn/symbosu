import ReactDOM from "react-dom";
import React from "react";
import httpGet from "../common/httpGet.js";
import { getUrlQueryParams } from "../common/queryParams.js";

class TaxaApp extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      tid: -1,
      sciName: '',
      basename: '',
      vernacularNames: [],
      images: [],
      isGardenTaxa: false,
    };
  }

  getTid() {
    return parseInt(this.props.tid);
  }

  componentDidMount() {
    httpGet(`./rpc/api.php?taxon=${this.props.tid}`)
      .then((res) => {
        res = JSON.parse(res);
        this.setState({
          sciName: res.sciname,
          basename: res.vernacular.basename,
          vernacularNames: res.vernacular.names,
          images: res.images,
          isGardenTaxa: res.isGardenTaxa
        });
        const pageTitle = document.getElementsByTagName("title")[0];
        pageTitle.innerHTML = `${pageTitle.innerHTML} ${res.sciname}`;
      })
      .catch((err) => {
        console.error(err);
      });
  }

  render() {
    return (
      <div className="container mt-5">
        <div className="row">
          <div className="col">
            <h1 className="text-capitalize">{ this.state.vernacularNames[0] }</h1>
            <h2 className="font-italic">{ this.state.sciName }</h2>
          </div>
          <div className="col-auto">
            <button className="d-block my-2 btn-primary">Printable page</button>
            <button className="d-block my-2 btn-secondary" disabled={ true }>Add to basket</button>
          </div>
        </div>
        <div className="row">
          <div id="col-0" className="col">
            <img
              id="img-main"
              src={ this.state.images.length > 0 ? this.state.images[0].url : '' }
              alt={ this.state.sciName }
            />
          </div>
          <div id="col-1" className="col-auto">
            <div id="highlights">
              <h3 className="text-light-green font-weight-bold">Highlights</h3>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

TaxaApp.defaultProps = {
  tid: -1,
};

const domContainer = document.getElementById("react-taxa-app");
ReactDOM.render(
  <TaxaApp tid={ getUrlQueryParams(window.location.search).taxon || TaxaApp.defaultProps.tid } />,
  domContainer
);