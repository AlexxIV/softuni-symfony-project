import React from 'react';

export default class Grades extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            grades: this.props.grades.map((grade, index) =>
                <tr key={index}>
                    <td>{grade.gradeName}</td>
                    <td>{grade.value}</td>
                    <td>{grade.notes}</td>
                    <td className="text-center">
                        <a href="#" className="btn-success btn grade-edit" id="btn-edit-grade">Edit</a>
                        <a href="#" className="btn-danger btn grade-delete" id="btn-delete-grade">Delete</a>
                    </td>
                </tr>
            )
        }
    }

    render() {
        return (
            <div>
                <div className="header" id="grades-header">Grades</div>
            <table cellSpacing="0" className="table-grades">
                <thead>
                <tr>
                    <th>Course*</th>
                    <th>Grade*</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {this.state.grades}
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td className="text-center">
                        <a href="{{ path('teacher_add_grades', {'student_id': student.id}) }}"
                           className="btn-primary btn"
                           id="grade-add">Add New</a>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        )
    }
}
