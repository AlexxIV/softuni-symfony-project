import React from 'react';
import moment from 'moment';

export default class Absences extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            absences: null,
        }
    }

    componentWillMount() {
        this.setState({
            absences: this.props.absences.map((absence, index) =>
                <tr key={index}>
                    <td>{absence.course}</td>
                    <td>{ moment.unix(absence.date.timestamp).format('DD-MM-YYYY')}</td>
                    <td>{absence.notes}</td>
                    <td>{absence.excused ? 'Yes' : 'No'}</td>
                    <td>
                        {absence.excused ? <a href="#" className="btn-success btn btn-excuse-absence" id={absence.id}>Excuse</a> : ''}
                    </td>
                </tr>)
        })
    }

    render() {
        return (
            <div>
                <div className="header" id="absences-header">Absences</div>
                <table cellSpacing="0" className="table-absences">
                    <thead>
                    <tr>
                        <th>Course*</th>
                        <th>Date*</th>
                        <th>Notes</th>
                        <th>Excused</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {this.state.absences}
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td className="text-center">
                            <a href="#"
                               className="btn-primary btn"
                               id="absence-add-new">Add New</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        )
    }
}