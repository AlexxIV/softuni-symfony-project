import React from 'react';
import ReactDOM from 'react-dom';
import Grades from "./components/Grades";
import Absences from "./components/Absences";

class StudentDetails extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            student: null,
            image: `https://res.cloudinary.com/schooldiarycloud/image/upload/h_150,r_50,w_150/v1/`,
            showGrades: false,
            showAbsences: false
        };

        this.handleGrades = this.handleGrades.bind(this);
        this.handleAbsences = this.handleAbsences.bind(this);
    }

    handleGrades() {
        this.setState({
            showGrades: !this.state.showGrades
        })
    }
    handleAbsences() {
        this.setState({
            showAbsences: !this.state.showAbsences
        })
    }

    componentWillMount() {
        this.setState((prevState) => ({
            student: JSON.parse(this.props.student),
            image: prevState.image + (JSON.parse(this.props.student)).email + '/' + (JSON.parse(this.props.student)).image
        }))
    };

    render() {
        return (
            <div className="wrapper">
                <div className="row mt-5">
                    <div className="col-12">
                        <div className="custom-table-temlate">
                            <div className="header">Student</div>
                            <table cellSpacing={0}>
                                <thead>
                                <tr>
                                    <th>Picture</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Personal ID</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><img src={this.state.image} alt=""/></td>
                                    <td>{this.state.student.firstName}</td>
                                    <td>{this.state.student.lastName}</td>
                                    <td>{this.state.student.email}</td>
                                    <td>{this.state.student.personalId}</td>
                                    <td>{this.state.student.studentClass.gradeForSelect}</td>
                                    <td className="text-center">
                                        <button onClick={this.handleGrades} className="btn-block btn-primary btn" id="btn-show-grades">Show
                                            Grades <span
                                                className="ml-2"> <i className="fas fa-arrow-right animation"></i></span></button>
                                        <button onClick={this.handleAbsences} className="btn-block btn-primary btn" id="btn-show-absences">Show
                                            Absences <span className="ml-2"> <i className="fas fa-arrow-right animation"></i></span></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            {this.state.showGrades ? <Grades grades={this.state.student.personalGrades}/> : null}
                            {this.state.showAbsences ? <Absences absences={this.state.student.absences}/> : null}
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

const root = document.getElementById('student-root');

ReactDOM.render(<StudentDetails {...(root.dataset)}/>, root);
