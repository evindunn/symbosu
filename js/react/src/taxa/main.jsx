import ReactDOM from "react-dom";
import React from "react";
import httpGet from "../common/httpGet.js";
import { getUrlQueryParams } from "../common/queryParams.js";

class TaxaApp extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      commonName: '',
      sciName: '',
      image: ''
    };
  }

  componentDidMount() {
    httpGet(`./rpc/api.php?taxon=${this.props.tid}`)
      .then((res) => {
        res = JSON.parse(res);
        this.setState({
          sciName: res.sciName,
          commonName: res.vernacularName,
          image: res.image
        });
      })
      .catch((err) => {
        console.error(err);
      });
  }

  render() {
    return (
      <div className="mt-5">
        <h1>{ this.state.commonName }</h1>
        <h2>{ this.state.sciName }</h2>
        <img src={ this.state.image } alt={ this.state.sciName } />
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